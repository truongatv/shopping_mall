<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Comment;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Contracts\OrderRepositoryInterface;
use App\Contracts\ProductRepositoryInterface;
use Elasticquent\ElasticquentTrait;
use Elasticsearch\ClientBuilder;
use App\Models\Category;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $productRepository;
    protected $orderRepository;

    public function __construct(ProductRepositoryInterface $productRepository,
        OrderRepositoryInterface $orderRepository)
    {
        $this->productRepository = $productRepository;
        $this->orderRepository = $orderRepository;
    }

    public function getProductDetails(Request $request)
    {
        $comments = $this->productRepository->getComments($request->product_id);
        $product = $this->productRepository->getProducts($request->product_id);
        $group = "";
        $name = "";

        if (!Auth::check()) {
            return view('product_details')->with(compact(
                'product',
                'comments',
                'group',
                'name'
                )
            );
        }
        else {
            $order = $this->orderRepository->getUserOrder();

            return view('product_details')->with(compact(
                'product',
                'comments',
                'order',
                'group',
                'name'
                )
            );

        }
    }

    public function getNewArrival(){
        $products = $this->productRepository->newArrivals()->paginate(6);
        $group = $name = "";
        if (!Auth::check()) {
            return view('All_Product')->with(compact(
                'products',
                'group',
                'name'
                )
            );
        }
        else {
            $order = $this->orderRepository->getUserOrder();

            return view('All_Product')->with(compact(
                'products',
                'order',
                'group',
                'name'
                )
            );

        }
    }

    public function getTopSell(){
        $products = $this->productRepository->topSells()->paginate(6);
        $group = $name = "";
        if (!Auth::check()) {
            return view('All_Product')->with(compact(
                'products',
                'group',
                'name'
                )
            );
        }
        else {
            $order = $this->orderRepository->getUserOrder();

            return view('All_Product')->with(compact(
                'products',
                'order',
                'group',
                'name'
                )
            );

        }
    }

    public function getCategoryProduct(Request $request){
        $products = $this->productRepository->categoryProduct($request->group, $request->name);
        $group = $name = "";
        if (!Auth::check()) {
            return view('All_Product')->with(compact(
                'products',
                'group',
                'name'
                )
            );
        }
        else {
            $order = $this->orderRepository->getUserOrder();

            return view('All_Product')->with(compact(
                'products',
                'order',
                'group',
                'name'
                )
            );
        }
    }

    public function search(Request $request){
        $this->validate($request, [
            'ecom-search' => 'regex:/(^([a-zA-z0-9]+)+(\d+)*)/u|min:1'
            ],[
            'ecom-search.required' => trans('errors.name'),
            'ecom-search.min' => trans('errors.name')
            ]);
        $name = $this->productRepository->search($request);
        return redirect()->route('search_name',$name);
    }

    public function searchName(Request $request, $name)
    {
        if($request->ajax()) {
            $type = $request['keyword'];
            $min = $request['min'];
            $max = $request['max'];
            $star = $request['star'];
            $order = $request['order'];
            if($star == 0) {
                $products = Product::complexSearchAndPaginate(array(
                    'body' => array(
                        'sort' => array(
                            array(
                                'unit_price' => $order
                            )
                        ),
                        'query' => array(
                            'bool' => array(
                                'must' => array(
                                    array(
                                        'multi_match' => array(
                                            'fields' => ['information', 'name'],
                                            'query' => $type
                                        )
                                    ),
                                    array(
                                        'match' => array(
                                            'status' => 1
                                        )
                                    ),
                                    array(
                                        'range' => array(
                                            'unit_price' => array(
                                                'from' => $min,
                                                'to' => $max
                                            )
                                        )
                                    ),
                                )
                            )
                        )
                    )
                ),6);
            }
            else {
                $products = Product::complexSearchAndPaginate(array(
                    'body' => array(
                        'query' => array(
                            'bool' => array(
                                'must' => array(
                                    array(
                                        'multi_match' => array(
                                            'fields' => ['information', 'name'],
                                            'query' => $type
                                        )
                                    ),
                                    array(
                                        'match' => array(
                                            'rate_count' => $star
                                        )
                                    ),
                                    array(
                                        'match' => array(
                                            'status' => 1
                                        )
                                    ),
                                    array(
                                        'range' => array(
                                            'unit_price' => array(
                                                'from' => $min,
                                                'to' => $max
                                            ),
                                        )
                                    ),

                                )
                            )
                        )
                    )
                ),6);
            }
            $htmlFilter = view('search.filter', compact('type', 'products'))->render();
            $result = [
                    'success' => true,
                    'search_result' => $htmlFilter
            ];
            return response()->json($result);
        }

        $products = Product::complexSearchAndPaginate(array(
            'body' => array(
                'sort' => array(
                    array(
                        'unit_price' => 'asc'
                    )
                ),
                'query' => array(
                    'bool' => array(
                        'must' => array(
                            array(
                                'multi_match' => array(
                                    'fields' => ['information', 'name'],
                                    'query' => $name
                                )
                            ),
                            array(
                                'match' => array(
                                    'status' => 1
                                )
                            ),
                        )
                    )
                )
            )
        ),6);
        $type = $name;
        $group = $name = "";
        if (!Auth::check()) {
            return view('search_product')->with(compact(
                'type',
                'products',
                'group',
                'name'
                )
            );
        }
        else {
            $order = $this->orderRepository->getUserOrder();

            return view('search_product')->with(compact(
                'type',
                'products',
                'order',
                'group',
                'name'
                )
            );
        }
    }
}

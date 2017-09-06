<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\ShopProduct;
use App\Models\Image;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\File;
use Illuminate\Contracts\Filesystem\Filesystem;
use DateTime;


class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getView()
    {
        return view('admin.category_master');
    }

    public function getList()
    {
        $category = Category::all();
        $categoryParent = Category::where('category_parent_id', NULL)->pluck('name', 'category_id')->toArray();
        $none['0'] = "None";
        $categoryParent = $none + $categoryParent;

        return view('admin.category', compact('categoryParent', 'category'));
    }

    public function getListChild($id)
    {
        $category = Category::where('category_parent_id', $id)->get();

        return view('admin.cate_child_list', ['category' => $category]);
    }

    public function getAdd()
    {
        $categoryParent = Category::where('category_parent_id', 0)->pluck('name', 'category_id')->toArray();
        $none['0'] = "None";
        $categoryParent = $none + $categoryParent;

        return view('admin.add_category', compact('categoryParent'));
    }

    public function postAdd(Request $request)
    {
        $this->validate($request,
            [
            'name' => 'required'
            ],
            [
            'name.requied' => 'Bạn chưa nhập category'
            ]);
        $category = new Category;
        $category->name = $request->name;
        $category->category_parent_id = $request->category_parent_id;
        $category->save();

        return redirect('admin/category/cate_list')->with('thongbao', 'add sucess !');
    }

    public function getEdit($id)
    {
        $category = Category::where('category_id', $id)->first();

        return view('admin.edit_category', ['category' => $category]);
    }

    public function postEdit(Request $request, $id)
    {
        $this->validate($request,
            [
                'name' => 'required|unique:categories,name'
            ],
            [
                'name.required' => 'Bạn Chưa nhập Category',
                'name.unique' => 'Category đã tồn tại'
            ]);
        $category =Category::where('category_id', $id)->first();
        $category->name = $request->name;
        $category->save();

        return redirect('admin/category/cate_list')->with('thongbao', 'Edit success !');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getDelete($id)
    {
        $category =  Category::find($id);
        $categoryChild = Category::where('category_parent_id', $id)->delete();
        $category->delete();

        return redirect('admin/category/cate_list')->with('thongbao1', 'Delete Success !');
    }

    public function getProductList()
    {
        $product = Product::where('status', 1)->get();
        return view('admin.product_list', ['product'=>$product]);
    }

    public function getAddProduct()
    {
        $category_parent = Category::where('category_parent_id', 0)->get();
        $category = Category::where('category_parent_id', $category_parent[0]['category_id'])->get();
        $shop_product = ShopProduct::all();
        return view('admin.add_product', ['category' => $category, 'shop_product' => $shop_product, 'category_parent' => $category_parent ]);
    }

    public function postAddProduct(Request $request)
    {
        $this->validate($request,
            [
            'name' => 'required',
            'unit_price' => 'numeric',
            'total_quanity' => 'numeric|min:1'
            ],
            [
            'name.requied' => trans('errors.product_name'),
            'unit_price.numeric' => trans('errors.unit_price'),
            'total_quanity' => trans('errors.total_quanity')
            ]);
        $product = new Product;
        $product->name = $request->name;
        $product->unit_price = $request->unit_price;
        $product->total_quanity = $request->total_quanity;
        $product->shop_product_id = $request->shop_product_id;
        $product->category_id = $request->category_id;
        $product->rate_count = 0 ;
        $product->top_product = $request->top_product;
        $product->information = $request->info_product;
        if(!$product->top_product) {
            $product->top_product = 100;
        }
        if(!$product->information) {
            $product->information = " ";
        }

        $product->save();
        $product->addToIndex();
        $category = Category::find($product->category_id);
        $shop_product = ShopProduct::find($product->shop_product_id);

        $file = $request->file('image_link');
        $name = time() . '_' . $file->getClientOriginalName();
        $file->move('assets/uploads', $name);
        $image = new Image;
        $image['link'] = $name;
        $image['product_id'] = $product->product_id;
        $image->save();
        $category->save();
        $shop_product->save();


        return redirect('admin/product/product_list')->with('thongbao', 'add sucess !');
    }

    public function getEditProduct($id)
    {
        $product = Product::find($id);

        return view('admin.edit_product', ['product'=>$product]);
    }

    public function postEditProduct(Request $request, $id)
    {
        $this->validate($request,
            [
            'name' => 'required',
            'unit_price' => 'numeric',
            'total_quanity' => 'numeric|min:1'
            ],
            [
            'name.requied' => trans('errors.product_name'),
            'unit_price.numeric' => trans('errors.unit_price'),
            'total_quanity' => trans('errors.total_quanity')
            ]);
        $product =Product::find($id);
        if ($request->name){
            $product->name = $request->name;
        }

        $product->unit_price = $request->unit_price;
        $product->total_quanity = $request->total_quanity;
        $product->top_product = $request->top_product;
        $product->information = $request->info_product;
        if(!$product->top_product) {
            $product->top_product = 100;
        }
        if(!$product->information) {
            $product->information = " ";
        }

        if ($request->image_link) {
            $file = $request->file('image_link');
            $name = time() . '_' . $file->getClientOriginalName();
            $file->move('assets/uploads', $name);
            // $image = Image::create([
            //     'link' => $name,
            //     'product_id' => $id,
            //     ]);
            $image = new Image;
            $image['link'] = $name;
            $image['product_id'] = $id;
            $image->save();

        }

        $product->save();
        $product->addToIndex();

        return redirect('admin/product/product_list')->with('thongbao', 'Edit success !');

    }

    public function getDeleteProduct($id)
    {
        DB::transaction(function() use ($id) {
            $product =  Product::find($id);
            $product->status = 0;
            $product->save();
        });

        return redirect('admin/product/product_list')->with('thongbao1', 'Delete Success !');
    }

    //Controller User
    public function getUserList()
    {
        $user = User::where('role', '<>', 3)->get();

        return view('admin.user_list', ['user'=>$user]);
    }

    public function getUserListAdmin()
    {
        $user = User::where('role',1)
            ->orwhere('role',2)
            ->get();

        return view('admin.admin_list', ['user'=>$user]);
    }
    public function getUserListCustomer()
    {
        $user = User::where('role', 0)->get();

        return view('admin.customer_list', ['user'=>$user]);
    }

    public function getDeleteUser($id)
    {
        $user =  User::find($id);
        $user->role = 3;
        $user->save();

        return redirect('admin/user/user_list')->with('thongbao1', 'Delete Success !');
    }

    public function getSetupUser($id)
    {
        $user =  User::find($id);
        if( $user->role == 0) {
            $user->role = 2;
        } elseif ($user->role == 2)
            $user->role = 0;
        $user->save();

        return redirect('admin/user/user_list');
    }

    //Controller Order
    public function getOrderList()
    {
        $order = Order::where('status', '<>', 2)->get();

        return view('admin.order_list', ['order'=>$order]);
    }
    public function getOrderListToday()
    {
        $date = new DateTime('today');
        $order = Order::where('created_at', '>', $date)->get();
        return view('admin.order_list', ['order'=>$order]);
    }
    public function getOrderListDoing()
    {
        $order = Order::where('status', 0)->get();

        return view('admin.order_list', ['order'=>$order]);
    }
    public function getOrderListDone()
    {
        $order = Order::where('status', 1)->get();

        return view('admin.order_list', ['order'=>$order]);
    }

    public function getDetailOrder($id)
    {
        $orderdetail = OrderDetail::where('order_id', $id)->get();
        $order = Order::where('order_id', $id)->get();

        return view('admin.detail_order', ['orderdetail' => $orderdetail, 'order' => $order]);
    }

    public function getDeleteOrder($id)
    {
        $order =  Order::find($id);
        $order->status = 2;
        $order->save();

        return redirect('admin/order/order_list')->with('thongbao1', 'Delete Success !');
    }
    public function getEditOrder($id)
    {
        $order =  Order::find($id);
        $order->status = 1;
        $order->save();

        return redirect('admin/order/order_list');
    }

}

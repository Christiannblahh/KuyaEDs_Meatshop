<?php

namespace App\Controllers;

use App\Models\ProductModel;

class Products extends BaseController
{
    protected ProductModel $products;

    public function __construct()
    {
        $this->products = new ProductModel();
    }

    public function index(): string
    {
        $data['products'] = $this->products->withStock();

        return view('layout', [
            'title'   => 'Products - Kuya EDs',
            'content' => view('products/index', $data),
        ]);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            $this->products->save([
                'name'               => $this->request->getPost('name'),
                'category'           => $this->request->getPost('category'),
                'unit'               => $this->request->getPost('unit'),
                'unit_price'         => $this->request->getPost('unit_price'),
                'low_stock_threshold'=> $this->request->getPost('low_stock_threshold') ?? 0,
                'description'        => $this->request->getPost('description'),
                'image_url'          => $this->request->getPost('image_url'),
            ]);

            return redirect()->to('/products');
        }

        return view('layout', [
            'title'   => 'Add Product - Kuya EDs',
            'content' => view('products/create'),
        ]);
    }
}



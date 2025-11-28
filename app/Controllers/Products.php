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
        $search = $this->request->getGet('search') ?? '';
        $category = $this->request->getGet('category') ?? '';
        
        $query = $this->products;
        
        if (!empty($search)) {
            $query = $query->like('name', $search);
        }
        
        if (!empty($category)) {
            $query = $query->where('category', $category);
        }
        
        $data['products'] = $query->withStock();
        $data['search'] = $search;
        $data['category'] = $category;
        
        // Get distinct categories
        $categoryResults = $this->products->distinct()->select('category')->where('category IS NOT NULL')->findAll();
        $data['categories'] = is_array($categoryResults) ? $categoryResults : [];

        return view('layout', [
            'title'   => 'Products - Kuya EDs',
            'content' => view('products/index', $data),
        ]);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            $validation = $this->validate([
                'name'        => 'required|min_length[3]|max_length[100]',
                'category'    => 'max_length[50]',
                'unit'        => 'required|max_length[20]',
                'unit_price'  => 'required|numeric|greater_than[0]',
                'low_stock_threshold' => 'numeric|greater_than_equal_to[0]',
                'description' => 'max_length[500]',
                'image_url'   => 'if_exist|valid_url_strict',
            ]);

            if (!$validation) {
                return view('layout', [
                    'title'   => 'Add Product - Kuya EDs',
                    'content' => view('products/create', ['errors' => $this->validator->getErrors()]),
                ]);
            }

            $this->products->save([
                'name'               => $this->request->getPost('name'),
                'category'           => $this->request->getPost('category'),
                'unit'               => $this->request->getPost('unit'),
                'unit_price'         => $this->request->getPost('unit_price'),
                'low_stock_threshold'=> $this->request->getPost('low_stock_threshold') ?? 0,
                'description'        => $this->request->getPost('description'),
                'image_url'          => $this->request->getPost('image_url'),
            ]);

            return redirect()->to('/products')->with('success', 'Product added successfully!');
        }

        return view('layout', [
            'title'   => 'Add Product - Kuya EDs',
            'content' => view('products/create'),
        ]);
    }

    public function edit($id = null)
    {
        $product = $this->products->find($id);
        if (!$product) {
            return redirect()->to('/products')->with('error', 'Product not found.');
        }

        if ($this->request->getMethod() === 'post') {
            $validation = $this->validate([
                'name'        => 'required|min_length[3]|max_length[100]',
                'category'    => 'max_length[50]',
                'unit'        => 'required|max_length[20]',
                'unit_price'  => 'required|numeric|greater_than[0]',
                'low_stock_threshold' => 'numeric|greater_than_equal_to[0]',
                'description' => 'max_length[500]',
                'image_url'   => 'if_exist|valid_url_strict',
            ]);

            if (!$validation) {
                return view('layout', [
                    'title'   => 'Edit Product - Kuya EDs',
                    'content' => view('products/edit', ['product' => $product, 'errors' => $this->validator->getErrors()]),
                ]);
            }

            $this->products->update($id, [
                'name'               => $this->request->getPost('name'),
                'category'           => $this->request->getPost('category'),
                'unit'               => $this->request->getPost('unit'),
                'unit_price'         => $this->request->getPost('unit_price'),
                'low_stock_threshold'=> $this->request->getPost('low_stock_threshold') ?? 0,
                'description'        => $this->request->getPost('description'),
                'image_url'          => $this->request->getPost('image_url'),
            ]);

            return redirect()->to('/products')->with('success', 'Product updated successfully!');
        }

        return view('layout', [
            'title'   => 'Edit Product - Kuya EDs',
            'content' => view('products/edit', ['product' => $product]),
        ]);
    }

    public function delete($id = null)
    {
        $product = $this->products->find($id);
        if (!$product) {
            return redirect()->to('/products')->with('error', 'Product not found.');
        }

        $this->products->delete($id);
        return redirect()->to('/products')->with('success', 'Product deleted successfully!');
    }
}



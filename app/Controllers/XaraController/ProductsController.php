<?php

class ProductsController extends \BaseController {

	/**
	 * Display a listing of products
	 *
	 * @return Response
	 */
	public function index()
	{
		$products = Product::all();

		

		return View::make('products.index', compact('products'));
	}

	/**
	 * Show the form for creating a new product
	 *
	 * @return Response
	 */
	public function create()
	{

		$vendors = Vendor::all();

		return View::make('products.create', compact('vendors'));
	}

	/**
	 * Store a newly created product in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Product::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}


			$file = Input::file('image');

			$filename = str_random(12);

			$ext = Input::file('image')->getClientOriginalExtension();
			$photo = $filename.'.'.$ext;

			
			$destination = public_path().'/uploads/images';
			
			
			Input::file('image')->move($destination, $photo);



		

		

		$vendor = Vendor::find(Input::get('vendor_id'));

		$product = new Product;

		$product->vendor()->associate($vendor);
		$product->name = Input::get('name');
		$product->image = $photo;
		$product->description = Input::get('description');
		$product->price = Input::get('price');
		$product->status = "active";
		$product->save();

		return Redirect::route('products.index');

	}

	/**
	 * Display the specified product.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$product = Product::findOrFail($id);

		return View::make('products.show', compact('product'));
	}

	/**
	 * Show the form for editing the specified product.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{

		

		$product = Product::find($id);
		$vendors = Vendor::all();

		return View::make('products.edit', compact('product', 'vendors'));
	}

	/**
	 * Update the specified product in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$product = Product::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Product::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

			


		$vendor = Vendor::find(Input::get('vendor_id'));

		$product->vendor()->associate($vendor);
		$product->name = Input::get('name');
		
		$product->description = Input::get('description');
		$product->price = Input::get('price');
		$product->status = 'active';
		$product->update();

		return Redirect::route('products.index');
	}

	/**
	 * Remove the specified product from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Product::destroy($id);

		return Redirect::route('products.index');
	}


	public function shop(){

		$products = Product::all();

		$loans = Loanproduct::all();

		return View::make('shop.index', compact('products', 'loans'));
	}

}

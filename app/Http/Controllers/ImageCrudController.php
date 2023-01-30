<?php

namespace App\Http\Controllers;

use App\Models\ImageCrud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class ImageCrudController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $images = ImageCrud::orderBy('id', 'desc')->get();

    return response()->json([
      'success' => true,
      'message' => $images
    ], 200);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create(Request $request)
  {
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $image = new ImageCrud();
    $validation = Validator::make($request->all(), [
      'title' => 'required',
      'image' => 'required|max:1024'
    ]);

    if ($validation->fails()) {
      return response()->json([
        'success' => false,
        'message' => $validation->errors()->all()
      ], 500);
    }

    $fileName = "";
    if ($request->hasFile('image')) {
      $fileName = $request->file('image')->store('posts', 'public');
    } else {
      $fileName = Null;
    }

    $image->title = $request->title;
    $image->image = $request->image;
    $result = $image->save();

    if ($result) {
      return response()->json([
        'success' => true,
        'message' => 'image saved'
      ], 201);
    } else {
      return response()->json([
        'success' => false,
        'message' => 'save failure'
      ], 500);
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $image = ImageCrud::findOrFail($id)->orderBy('id', 'desc')->get();

    return response()->json([
      'success' => true,
      'message' => $image
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $image = ImageCrud::findOrFail($id);

    $destination = public_path("storage\\" . $image->image);
    $filename = "";
    if ($request->hasFile('new_image')) {
      if (File::exists($destination)) {
        File::delete($destination);
      }

      $filename = $request->file('new_image')->store('posts', 'public');
    } else {
      $filename = $image->image;
    }

    $image->title = $request->title;
    $image->image = $filename;
    $result = $image->save();

    if ($result) {
      return response()->json([
        'success' => true,
        'message' => $image
      ]);
    } else {
      return response()->json([
        'success' => false,
        'message' => "update failure"
      ]);
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $image = ImageCrud::findOrFail($id);
    $destination = public_path("storage\\" . $image->image);

    if (File::exists($destination)) {
      File::delete($destination);
    }

    $result = $image->delete();

    if ($result) {
      return response()->json([
        'success' => true,
        'message' => 'delete successfull'
      ]);
    } else {
      return response()->json([
        'success' => false,
        'message' => 'delete failure'
      ]);
    }
  }
}

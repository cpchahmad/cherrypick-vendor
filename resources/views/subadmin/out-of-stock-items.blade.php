@extends('layouts.admin')

  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>Product - Out of Stock</h1>
    </div><!-- End Page Title -->
   </div>
   <div class="member-plan-search header onetime-search">
    <div class="search-bar">
        <form class="search-form d-flex align-items-center" method="POST" action="#">
          <label>Search Products</label>
          <input type="text" name="query" placeholder="Search Product Name" title="Enter search keyword">
          <button type="submit" title="Search"><i class="bi bi-search"></i></button>
        </form>
      </div>
      <div class="create-plan">
        <a href="#">Add New Product</a>
      </div>
   </div>
    <section class="section up-banner">
        <form class="add-product-form">
                <div class="card table-card">
                  <div class="table-responsive">
                    <table class="table table-borderless view-productd">
                        <thead>
                          <tr>
                            <th scope="col">Preview</th>
                            <th scope="col">Product</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Update Quantity</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <th scope="row"><a href="#"><img src="{{url('subadmin/assets/img/product-1.jpg')}}" alt=""></a></th>
                            <td><a href="#" class="text-primary fw-bold">Ut inventore ipsa voluptas nulla</a></td>
                            <td class="fw-bold">0</td>
                            <td class="up-quantity">
                                <input type="text" name="qtn">
                                <button type="button">Save</button>
                            </td>
                          </tr>
                          <tr>
                            <th scope="row"><a href="#"><img src="{{url('subadmin/assets/img/product-2.jpg')}}" alt=""></a></th>
                            <td><a href="#" class="text-primary fw-bold">Exercitationem similique doloremque</a></td>
                            <td class="fw-bold">0</td>
                            <td class="up-quantity">
                                <input type="text" name="qtn">
                                <button type="button">Save</button>
                            </td>
                          </tr>
                          <tr>
                            <th scope="row"><a href="#"><img src="{{url('subadmin/assets/img/product-3.jpg')}}" alt=""></a></th>
                            <td><a href="#" class="text-primary fw-bold">Doloribus nisi exercitationem</a></td>
                            <td class="fw-bold">0</td>
                            <td class="up-quantity">
                                <input type="text" name="qtn">
                                <button type="button">Save</button>
                            </td>
                          </tr>
                          <tr>
                            <th scope="row"><a href="#"><img src="{{url('subadmin/assets/img/product-4.jpg')}}" alt=""></a></th>
                            <td><a href="#" class="text-primary fw-bold">Officiis quaerat sint rerum error</a></td>
                            <td class="fw-bold">0</td>
                            <td class="up-quantity">
                                <input type="text" name="qtn">
                                <button type="button">Save</button>
                            </td>
                          </tr>
                          <tr>
                            <th scope="row"><a href="#"><img src="{{url('subadmin/assets/img/product-5.jpg')}}" alt=""></a></th>
                            <td><a href="#" class="text-primary fw-bold">Sit unde debitis delectus repellendus</a></td>
                            <td class="fw-bold">0</td>
                            <td class="up-quantity">
                                <input type="text" name="qtn">
                                <button type="button">Save</button>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                </div>
        </form>
    </section>
   </main>
  <!-- End #main -->
  <!-- ======= Footer ======= -->
  
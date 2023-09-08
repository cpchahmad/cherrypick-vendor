@extends('layouts.admin')

  <main id="main" class="main">
    <div class="home-flex">
        <div class="pagetitle fit-title">
            <h1>View Users</h1>
        </div><!-- End Page Title -->
        </div>
        <div class="member-plan-search header onetime-search">
            <div class="search-bar">
                <form class="search-form d-flex align-items-center" method="POST" action="#">
                  <label>Search User</label>
                  <input type="text" name="query" placeholder="Search User" title="Enter search keyword">
                  <button type="submit" title="Search"><i class="bi bi-search"></i></button>
                </form>
              </div>
              <div class="create-plan">
                <a href="add-user.html">Add User</a>
              </div>
           </div>
           <section class="section up-banner">
            <form class="add-product-form">
                    <div class="card table-card">
                      <div class="table-responsive">
                        <table class="table table-borderless view-productd">
                            <thead>
                              <tr>
                                <th scope="col">S.No</th>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Phone</th>
                                <th scope="col">Role</th>
                                <th scope="col">Action</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td>1</td>
                                <td>Test Name</td>
                                <td>info@gmail.com</td>
                                <td>91 XXX XXXX XXX</td>
                                <td>Marketing</td>
                                <td class="icon-action">
                                    <a href="add-user.html"><i class="bi bi-pencil-fill"></i></a>
                                    <a href="#verticalycentered"  data-bs-toggle="modal"><i class="bi bi-trash"></i></a>
                                </td>
                              </tr>
                              <tr>
                                <td>2</td>
                                <td>Test Name</td>
                                <td>info@gmail.com</td>
                                <td>91 XXX XXXX XXX</td>
                                <td>Marketing</td>
                                <td class="icon-action">
                                    <a href="add-user.html"><i class="bi bi-pencil-fill"></i></a>
                                    <a href="#verticalycentered"  data-bs-toggle="modal"><i class="bi bi-trash"></i></a>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                    </div>
            </form>
        </section>
        <div class="modal fade" id="verticalycentered" tabindex="-1" data-bs-backdrop="false">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title"><b><i class="bi bi-trash"></i></b> Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                Are you sure you want to Delete ?
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                <button type="button" class="btn btn-primary">Delete!</button>
              </div>
            </div>
          </div>
        </div>
   </main>
  <!-- End #main -->
  <!-- ======= Footer ======= -->
  
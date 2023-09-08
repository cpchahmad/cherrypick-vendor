
@extends('layouts.admin')
  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle fit-title">
      <h1>Discount</h1>
    </div><!-- End Page Title -->
   </div>
   <div class="member-plan-search header onetime-search">
    <div class="search-bar">
        <form class="search-form d-flex align-items-center" method="GET" action="{{url('discountlist')}}">
            <label>Search Discount=</label>
          <input type="text" name="code" placeholder="Search Discount Name" title="Enter search keyword">
          <button type="submit" title="Search"><i class="bi bi-search"></i></button>
        </form>
      </div>
      <div class="create-plan">
        <a href="discount-setting.html">Add Discount</a>
      </div>
   </div>
   <section class="section dashboard">
    <div class="row">
      <div class="col-lg-12">
          <div class="card">
              <div class="card-body show-plan collections disct">
                <!-- Bordered Table -->
                <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th scope="col"><input class="form-check-input" type="checkbox" id="gridCheck"></th>
                      <th scope="col">Product Name</th>
                      <th scope="col">Discount Code</th>
                      <th scope="col">Discount Type</th>
                      <th scope="col">Discount Value</th>
                      <th scope="col">Status</th>
                      <th scope="col">Settings</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <th scope="row">
                        <input class="form-check-input" type="checkbox" id="gridCheck">
                     </th>
                      <td>T-Shirt</td>
                      <td>JSJW12G</td>
                      <td>Percentage</td>
                      <td>30 %</td>
                      <td>
                        <span class="form-switch">
                         <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" checked>
                        </span>
                    </td>
                      <td class="icon-action">
                          <a href="discount-setting.html"><i class="bi bi-gear"></i></a>
                      </td>
                    </tr>
                    <tr>
                        <th scope="row">
                          <input class="form-check-input" type="checkbox" id="gridCheck">
                       </th>
                        <td>T-Shirt</td>
                        <td>JSJW12S</td>
                        <td>Percentage</td>
                        <td>10 %</td>
                        <td>
                          <span class="form-switch">
                           <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
                          </span>
                      </td>
                        <td class="icon-action">
                            <a href="discount-setting.html"><i class="bi bi-gear"></i></a>
                        </td>
                      </tr>
                      <tr>
                        <th scope="row">
                          <input class="form-check-input" type="checkbox" id="gridCheck">
                       </th>
                        <td>Box</td>
                        <td>JSJW12K</td>
                        <td>Percentage</td>
                        <td>20 %</td>
                        <td>
                          <span class="form-switch">
                           <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" checked>
                          </span>
                      </td>
                        <td class="icon-action">
                            <a href="discount-setting.html"><i class="bi bi-gear"></i></a>
                        </td>
                      </tr>
                  </tbody>
                </table>
                </div>
                <!-- End Bordered Table -->
  
              </div>
            </div>
      </div>
    </div>
    <nav class="mainpg timer-nav">
      <ul class="pagination">
        <li class="page-item disabled">
          <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
        </li>
        <li class="page-item"><a class="page-link" href="#">1</a></li>
        <li class="page-item active" aria-current="page">
          <a class="page-link" href="#">2</a>
        </li>
        <li class="page-item"><a class="page-link" href="#">3</a></li>
        <li class="page-item">
          <a class="page-link" href="#">Next</a>
        </li>
      </ul>
    </nav>
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
  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  
@extends('layouts.admin')

  <main id="main" class="main">
    <div class="home-flex">
        <div class="pagetitle fit-title">
            <h1>User Role</h1>
        </div><!-- End Page Title -->
        </div>
         <section class="section up-banner">
            <form class="add-product-form">
                <div class="card">
                    <div class="row">
                   <div class="col-12">
                       <label for="inputNanme4" class="form-label">Role name</label>
                       <input type="text" class="form-control" id="" value="" placeholder="Enter role name">
                      </div>
                      <div class="col-12 fnuser-role">
                        <p>Module permission :</p>
                        <div class="row">
                        <div class="col-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="gridCheck1">
                            <label class="form-check-label" for="gridCheck1"> Store Configuration </label>
                          </div>
                        </div>
                        <div class="col-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="gridCheck2">
                                <label class="form-check-label" for="gridCheck2"> Products </label>
                              </div>
                            </div>
                            <div class="col-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="gridCheck3">
                                <label class="form-check-label" for="gridCheck3"> Orders </label>
                                </div>
                            </div>
                        <div class="col-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="gridCheck4">
                                <label class="form-check-label" for="gridCheck4"> Marketing </label>
                            </div>
                            </div>
                    </div>
                       </div>
                 </div>
           </div>
           <div class="timer-btns">
            <button type="submit" class="btn btn-primary">Submit</button>
         </div>
            </form>
        </section>
   </main>
  <!-- End #main -->
  <!-- ======= Footer ======= -->
  
@extends('layouts.admin')

  <main id="main" class="main">
    <div class="subpagetitle fit-title">
        <h1>Discount Settings</h1>
         <p><a href="discount.html">Discount</a> / <b>Discount Settings</b></p>
      </div>
      
   <section class="section dashboard">
    <div class="row">
      <div class="col-lg-12">
        <form class="edit-timer-form cp-setting">
            <div class="form-switch">
                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" checked>
                <label class="form-check-label" for="flexSwitchCheckDefault">Enable Discount</label>
               </div>

            <div class="card">
                <div class="seprate row g-3">
                    <div class="col-12">
                     <label for="inputNanme4" class="form-label"><b>Discount Code</b></label>
                     <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="gridCheck1" checked>
                        <label class="form-check-label" for="gridCheck1">
                         Enable Discount
                        </label>
                      </div>
                     <input type="text" class="form-control" id="" placeholder="Discount Code">
                     <span class="ds-text">Customers will enter this discount code at checkout.</span>
                    </div>
               </div>
            </div>

            <div class="card">
            <div class="seprate row g-3">
                <div class="col-6">
                    <label for="" class="form-label"><b>Type</b></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios1" value="option1" checked>
                        <label class="form-check-label" for="gridRadios1">
                            Percentage
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios2" value="option2">
                        <label class="form-check-label" for="gridRadios2">
                            Fixed Discount
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios3" value="option3">
                        <label class="form-check-label" for="gridRadios3">
                            Buy X get Y
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios4" value="option4">
                        <label class="form-check-label" for="gridRadios4">
                            Free Shipping
                        </label>
                    </div>
                </div>
                <div class="col-6">
                    <div class="col-12">
                        <label for="inputNanme4" class="form-label"><b>Value</b></label>
                        <input type="text" class="form-control" id="" placeholder="Discount Value">
                        <span class="ds-text">Example: 30%</span>
                       </div>
                </div>
              </div>     
           </div>
           <div class="card">
            <div class="seprate row g-3">
                <div class="col-6">
                    <label for="" class="form-label"><b>Minimum Requirement</b></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gridMR" id="gridMR1" value="option1" checked>
                        <label class="form-check-label" for="gridMR1">
                            None
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gridMR" id="gridMR2" value="option2">
                        <label class="form-check-label" for="gridMR2">
                            Minimum Purchase Price ($)
                        </label>
                        <div class="show-fields-input">
                          <input class="form-control" type="text" placeholder="Enter Minimum Price">
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gridMR" id="gridMR3" value="option3">
                        <label class="form-check-label" for="gridMR3">
                            Minimum Quantity of Items
                        </label>
                        <div class="show-fields-input">
                          <input class="form-control" type="text" placeholder="Enter Minimum Quantity">
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="col-12">
                        <label for="inputNanme4" class="form-label"><b>Usage Limits</b></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="gridUL" id="gridUL1" value="option1">
                            <label class="form-check-label" for="gridUL1">
                                Limit number of times this discount can be used in total
                            </label>
                            <div class="show-fields-input">
                              <input class="form-control" type="text" placeholder="Enter Limit number">
                            </div>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="gridUL" id="gridUL2" value="option2">
                            <label class="form-check-label" for="gridUL2">
                                Limit to one use per customer
                            </label>
                        </div>
                       </div>
                </div>
              </div>     
           </div>
           <div class="card">
            <div class="seprate row g-3">
                <div class="col-12">
                    <label for="" class="form-label"><b>Applies To</b></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gridAT" id="gridAT" value="option1" checked>
                        <label class="form-check-label" for="gridAT">
                            Entire Products
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gridAT" id="gridAT1" value="option2">
                        <label class="form-check-label" for="gridAT1">
                            Specific Collection
                        </label>
                        <div class="member-plan-search header browser-search">
                          <div class="search-bar search-form d-flex align-items-center">
                                <input type="text" name="query" placeholder="Search Collection" title="Enter search keyword">
                                <button type="submit" title="Search"><i class="bi bi-search"></i></button>
                            </div>
                         </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gridAT" id="gridAT2" value="option3">
                        <label class="form-check-label" for="gridAT2">
                            Specific Products
                        </label>
                        <div class="member-plan-search header browser-search">
                          <div class="search-bar search-form d-flex align-items-center">
                                <input type="text" name="query" placeholder="Search Products" title="Enter search keyword">
                                <button type="submit" title="Search"><i class="bi bi-search"></i></button>
                            </div>
                         </div>
                    </div>
                   
                </div>
              </div>     
           </div>
           <div class="card">
            <div class="seprate row g-3">
                <div class="col-12">
                    <label for="" class="form-label"><b>Customer Eligibility</b></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gridCE" id="gridCE" value="option1" checked>
                        <label class="form-check-label" for="gridCE">
                            Everyone
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gridCE" id="gridCE1" value="option2">
                        <label class="form-check-label" for="gridCE1">
                            Specific Group of Customer
                        </label>
                        <div class="member-plan-search header browser-search">
                          <div class="search-bar search-form d-flex align-items-center">
                                <input type="text" name="query" placeholder="Search Group Customer" title="Enter search keyword">
                                <button type="submit" title="Search"><i class="bi bi-search"></i></button>
                            </div>
                         </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gridCE" id="gridCE2" value="option3">
                        <label class="form-check-label" for="gridCE2">
                            Specific Customers
                        </label>
                        <div class="member-plan-search header browser-search">
                          <div class="search-bar search-form d-flex align-items-center">
                                <input type="text" name="query" placeholder="Search Customers" title="Enter search keyword">
                                <button type="submit" title="Search"><i class="bi bi-search"></i></button>
                            </div>
                         </div>
                    </div>
                   
                </div>
              </div>     
           </div>
           <div class="card">
            <div class="seprate offer row g-3">
                <div class="col-12">
                 <p><b>Active Dates</b></p>
                 <div class="row">
                 <div class="col-6">
                 <label for="inputNanme4" class="form-label">Start Date</label>
                 <input type="date" class="form-control" id="" value="">
               
                </div>
                <div class="col-6">
                    <label for="inputNanme4" class="form-label">Start Time</label>
                    <input type="time" class="form-control" id="" placeholder="Offer Messages">
                    
                   </div>
                   <div class="col-12">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="gridEN" id="gridEN" value="option3">
                      <label class="form-check-label" for="gridEN">
                          Set End Date
                      </label>
                      <div class="end-date-time">
                      <div class="row">
                        <div class="col-6">
                          <label for="inputNanme4" class="form-label">End Date</label>
                          <input type="date" class="form-control" id="" value="">
                        
                         </div>
                         <div class="col-6">
                             <label for="inputNanme4" class="form-label">End Time</label>
                             <input type="time" class="form-control" id="" placeholder="Offer Messages">
                             
                            </div>
                      </div>
                    </div>
                  </div>
                   </div>
                </div>
            </div>
           </div>
        </div>
 
    <div class="timer-btns">
        <button type="reset" class="btn btn-secondary">Back</button>
        <button type="submit" class="btn btn-primary">Save</button>
     </div>
 </form>
      </div>
    </div>
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
  
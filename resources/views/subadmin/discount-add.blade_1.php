@extends('layouts.admin')
@section('main')
<main id="main" class="main">
    <div class="subpagetitle fit-title">
        <h1>Discount Settings</h1>
         <p><a href="discount.html">Discount</a> / <b>Discount Settings</b></p>
      </div>
      
   <section class="section dashboard">
    <div class="row">
      <div class="col-lg-12">
          <form class="edit-timer-form cp-setting" method="post" action="{{url('save-discount')}}">
              @csrf
<!--            <div class="form-switch">
                <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" checked>
                <label class="form-check-label" for="flexSwitchCheckDefault">Enable Discount</label>
               </div>-->

            <div class="card">
                <div class="seprate row g-3">
                    <div class="col-12">
                     <label for="inputNanme4" class="form-label"><b>Discount Code</b></label>
<!--                     <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="gridCheck1" checked>
                        <label class="form-check-label" for="gridCheck1">
                         Enable Discount
                        </label>
                      </div>-->
<input type="text" class="form-control" id="" name="discount_code" placeholder="Discount Code" required="true">
                     <span class="ds-text">Customers will enter this discount code at checkout.</span>
                    </div>
               </div>
            </div>

            <div class="card">
            <div class="seprate row g-3">
                <div class="col-6">
                    <label for="" class="form-label"><b>Type</b></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="discount_type" id="gridRadios1" value="percentage" checked>
                        <label class="form-check-label" for="gridRadios1">
                            Percentage
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="discount_type" id="gridRadios2" value="fixed_amount">
                        <label class="form-check-label" for="gridRadios2">
                            Fixed Discount
                        </label>
                    </div>
<!--                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="discount_type" id="gridRadios3" value="buy_x_get_y">
                        <label class="form-check-label" for="gridRadios3">
                            Buy X get Y
                        </label>
                    </div>-->
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="discount_type" id="gridRadios4" value="free_shipping">
                        <label class="form-check-label" for="gridRadios4">
                            Free Shipping
                        </label>
                    </div>
                </div>
                <div class="col-6">
                    <div class="col-12">
                        <label for="inputNanme4" class="form-label"><b>Value</b></label>
                        <input type="text" class="form-control" id="" name="discount_value" placeholder="Discount Value"  required="true">
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
                        <input class="form-check-input" type="radio" name="minimum_requirements" id="gridMR1" value="none" checked>
                        <label class="form-check-label" for="gridMR1">
                            None
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="minimum_requirements" id="gridMR2" value="price">
                        <label class="form-check-label" for="gridMR2">
                            Minimum Purchase Price ($)
                        </label>
                        <div class="show-fields-input">
                          <input class="form-control" type="text" name="minimum_price" placeholder="Enter Minimum Price">
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="minimum_requirements" id="gridMR3" value="quantity">
                        <label class="form-check-label" for="gridMR3">
                            Minimum Quantity of Items
                        </label>
                        <div class="show-fields-input">
                            <input class="form-control" type="text" name="minimum_quantity" placeholder="Enter Minimum Quantity">
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="col-12">
                        <label for="inputNanme4" class="form-label"><b>Usage Limits</b></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="usage_limit" id="gridUL1" value="2">
                            <label class="form-check-label" for="gridUL1">
                                Limit number of times this discount can be used in total
                            </label>
                            <div class="show-fields-input">
                                <input class="form-control" type="text" name="usage_value" placeholder="Enter Limit number">
                            </div>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="usage_limit" id="gridUL2" value="1">
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
                        <input class="form-check-input" type="radio" name="applies_to" id="gridAT" value="all_product" checked>
                        <label class="form-check-label" for="gridAT">
                            Entire Products
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="applies_to" id="gridAT1" value="collection">
                        <label class="form-check-label" for="gridAT1">
                            Specific Collection
                        </label>
                        <div class="member-plan-search header browser-search">
                          <div class="search-bar search-form d-flex align-items-center">
                                <input type="text" name="collection_ids" placeholder="Search Collection" title="Enter search keyword">
                                <button type="submit" title="Search"><i class="bi bi-search"></i></button>
                            </div>
                         </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="applies_to" id="gridAT2" value="products">
                        <label class="form-check-label" for="gridAT2">
                            Specific Products
                        </label>
                        <div class="member-plan-search header browser-search">
                          <div class="search-bar search-form d-flex align-items-center">
                                <input type="text" name="products_ids" placeholder="Search Products" title="Enter search keyword">
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
                        <input class="form-check-input" type="radio" name="eligibility" id="gridCE" value="everyone" checked>
                        <label class="form-check-label" for="gridCE">
                            Everyone
                        </label>
                    </div>
<!--                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="eligibility" id="gridCE1" value="group">
                        <label class="form-check-label" for="gridCE1">
                            Specific Group of Customer
                        </label>
                        <div class="member-plan-search header browser-search">
                          <div class="search-bar search-form d-flex align-items-center">
                                <input type="text" name="group_ids" placeholder="Search Group Customer" title="Enter search keyword">
                                <button type="submit" title="Search"><i class="bi bi-search"></i></button>
                            </div>
                         </div>
                    </div>-->
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="eligibility" id="gridCE2" value="customers">
                        <label class="form-check-label" for="gridCE2">
                            Specific Customers
                        </label>
                        <div class="member-plan-search header browser-search">
                          <div class="search-bar search-form d-flex align-items-center">
                                <input type="text" name="customer_ids" placeholder="Search Customers" title="Enter search keyword">
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
                 <input type="date" name="start_date" class="form-control" id="" value=""  required="true">
               
                </div>
                <div class="col-6">
                    <label for="inputNanme4" class="form-label">Start Time</label>
                    <input type="time" name="start_time" class="form-control" id="" placeholder="Offer Messages"  required="true">
                    
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
                          <input type="date" name="end_date" class="form-control" id="" value="">
                        
                         </div>
                         <div class="col-6">
                             <label for="inputNanme4" class="form-label">End Time</label>
                             <input type="time" name="end_time" class="form-control" id="" placeholder="Offer Messages">
                             
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
  </main><!-- End #main -->
@endsection
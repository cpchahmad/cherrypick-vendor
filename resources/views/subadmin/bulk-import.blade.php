@extends('layouts.admin')
  <main id="main" class="main">
   <div class="home-flex">
    <div class="pagetitle">
       <h1>Product - Bulk Import</h1>
    </div><!-- End Page Title -->
   </div>
     
    <section class="section up-banner">
        <div class="bulk-intcn">
        <h2>Foods Bulk Import</h2>
        <h4>Instructions :</h4>
        <ol>
            <li>Download the CSV format file and fill it with proper data.</li>
            <li>You can download the example file to understand how the data must be filled.</li>
            <li>Once you have downloaded and filled the format file, upload it in the form below and submit.</li>
            <li>After uploading products you need to edit them and set image and variations.</li>
            <li>You can get category id from their list, please input the right ids.</li>
            <li>Don't forget to fill all the fields</li>
        </ol>
      </div>
        <form class="add-product-form">
            <div class="card">
              <div class="bulk-example">
                <p><b>Import CSV Products File</b></p>
                <p><a href="#" download="">Download Format</a></p>
              </div>
              <div class="row mb-3 file-download">
                <label for="inputNumber" class="col-sm-2 col-form-label">Upload CSV File</label>
                <div class="col-sm-10">
                  <input class="form-control" type="file" id="formFile">
                </div>  
              </div>
            </div>
            <div class="timer-btns pro-submit">
              <button type="submit" class="btn btn-primary">Submit</button>
           </div>
        </form>
    </section>
   </main>
  <!-- End #main -->
  <!-- ======= Footer ======= -->
  
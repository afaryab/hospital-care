
<div class="page-content-wrapper">
    <div class="page-content">
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <i class="icon-home"></i>
                    <span>Dashboard</span>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <i class="glyphicon glyphicon-stats"></i>
                    <span>Today</span>
                    <i class="fa fa-angle-down"></i>
                </li>
            </ul>

        </div>
        <!-- BEGIN DASHBOARD STATS -->
        <div class="row">
            <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6">
                <div class="dashboard-stat green-haze">
                    <div class="visual">
                    <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <div class="details">
                        <div class="number" id="today_sales">0
                        </div>
                        <div class="desc">
                             Income
                        </div>
                    </div>
                    <div style="display: block; width:100%; margin-top:100px;">
                        <div style="border-bottom: 1px solid #ccc; padding:5px 15px;">
                            <span class="text-white">This Week</span>
                            <span class="text-white pull-right" id="this_week_income">0</span>
                        </div>
                        <div style="border-bottom: 1px solid #ccc; padding:5px 15px;">
                            <span class="text-white">This Month</span>
                            <span class="text-white pull-right" id="this_month_income">0</span>
                        </div>
                        <div style="border-bottom: 1px solid #ccc; padding:5px 15px;">
                            <span class="text-white">This Year</span>
                            <span class="text-white pull-right" id="this_year_income">0</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6">
                <div class="dashboard-stat red-pink">
                    <div class="visual">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="details">
                        <div class="number" id="today_expense">0
                        </div>
                        <div class="desc">
                             Expense
                        </div>
                    </div>
                    <div style="display: block; width:100%; margin-top:100px;">
                        <div style="border-bottom: 1px solid #ccc; padding:5px 15px;">
                            <span class="text-white">This Week</span>
                            <span class="text-white pull-right" id="this_week_expense">0</span>
                        </div>
                        <div style="border-bottom: 1px solid #ccc; padding:5px 15px;">
                            <span class="text-white">This Month</span>
                            <span class="text-white pull-right" id="this_month_expense">0</span>
                        </div>
                        <div style="border-bottom: 1px solid #ccc; padding:5px 15px;">
                            <span class="text-white">This Year</span>
                            <span class="text-white pull-right" id="this_year_expense">0</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-4 col-xs-6">
                <div class="dashboard-stat blue-madison">
                    <div class="visual">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="details">
                        <div class="number" id="today_patients">0
                        </div>
                        <div class="desc">
                             Patients
                        </div>
                    </div>
                    <div style="display: block; width:100%; margin-top:100px;">
                        <div style="border-bottom: 1px solid #ccc; padding:5px 15px;">
                            <span class="text-white">This Week</span>
                            <span class="text-white pull-right" id="this_week_patients">0</span>
                        </div>
                        <div style="border-bottom: 1px solid #ccc; padding:5px 15px;">
                            <span class="text-white">This Month</span>
                            <span class="text-white pull-right" id="this_month_patients">0</span>
                        </div>
                        <div style="border-bottom: 1px solid #ccc; padding:5px 15px;">
                            <span class="text-white">This Year</span>
                            <span class="text-white pull-right" id="this_year_patients">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-6">
                <div class="dashboard-stat blue-madison">
                    <div class="visual">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="details">
                        <div class="number" id="today_sales">Headers
                        </div>
                        <div class="desc">
                             Today
                        </div>
                    </div>
                    <div style="display: block; width:100%; margin-top:100px;" id="today_headers">
                        
                    </div>
                    <div class="details" style="margin-top:20px;">
                        <div class="desc">
                             This Week
                        </div>
                    </div>
                    <div style="display: block; width:100%; margin-top:50px;" id="this_week_headers">
                        
                    </div>

                    <div class="details" style="margin-top:20px;">
                        <div class="desc">
                             This Month
                        </div>
                    </div>
                    <div style="display: block; width:100%; margin-top:50px;" id="this_month_headers">
                        
                    </div>

                    <div class="details" style="margin-top:20px;">
                        <div class="desc">
                             This Year
                        </div>
                    </div>
                    <div style="display: block; width:100%; margin-top:50px;" id="this_year_headers">
                        
                    </div>


                </div>
            </div>
        </div>

    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.min.js"></script>
<script>
    $(function(){
        var sourceUrl = "<?= site_url($DASHBOARD_JSON) ?>";
        $.ajax( sourceUrl )
            .done(function( response ) {

                var data = $.parseJSON(response);
                console.log(data);
                $('#today_sales').html(data.transactions.today.INCOME);
                $('#this_week_income').html(data.transactions.this_week.INCOME);
                $('#this_month_income').html(data.transactions.this_month.INCOME);
                $('#this_year_income').html(data.transactions.this_year.INCOME);

                $('#today_expense').html(data.transactions.today.EXPENSE);
                $('#this_week_expense').html(data.transactions.this_week.EXPENSE);
                $('#this_month_expense').html(data.transactions.this_month.EXPENSE);
                $('#this_year_expense').html(data.transactions.this_year.EXPENSE);

                $('#today_patients').html(data.patients.today);
                $('#this_week_patients').html(data.patients.this_week);
                $('#this_month_patients').html(data.patients.this_month);
                $('#this_year_patients').html(data.patients.this_year);

                let todayHeaders = '';

                $.each(data.headers.today, function(index, value){
                    
                    let temphhtml = '<div style="border-bottom: 1px solid #ccc; padding:5px 15px;"><span class="text-white">'+index+'</span><span class="text-white pull-right">'+value+'</span></div>';
                    todayHeaders += temphhtml;

                });

                let thisWeekHeaders = '';

                $.each(data.headers.this_week, function(index, value){
                    
                    let temphhtml = '<div style="border-bottom: 1px solid #ccc; padding:5px 15px;"><span class="text-white">'+index+'</span><span class="text-white pull-right">'+value+'</span></div>';
                    thisWeekHeaders += temphhtml;
                    
                });

                let thisMonthHeaders = '';

                $.each(data.headers.this_month, function(index, value){
                    
                    let temphhtml = '<div style="border-bottom: 1px solid #ccc; padding:5px 15px;"><span class="text-white">'+index+'</span><span class="text-white pull-right">'+value+'</span></div>';
                    thisMonthHeaders += temphhtml;
                    
                });

                let thisYearHeaders = '';

                $.each(data.headers.this_year, function(index, value){
                    
                    let temphhtml = '<div style="border-bottom: 1px solid #ccc; padding:5px 15px;"><span class="text-white">'+index+'</span><span class="text-white pull-right">'+value+'</span></div>';
                    thisYearHeaders += temphhtml;
                    
                });


                $('#today_headers').html(todayHeaders);
                $('#this_week_headers').html(thisWeekHeaders);
                $('#this_month_headers').html(thisMonthHeaders);
                $('#this_year_headers').html(thisYearHeaders);
            });
    });
</script>
<div style="border-bottom: 1px solid #ccc; padding:5px 15px;">
    <span class="text-white">This Week</span>
    <span class="text-white pull-right" id="this_week_expense">0</span>
</div>
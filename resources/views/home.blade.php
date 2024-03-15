@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid" style="width:100%">
        
                    @if (session('status'))
                        {{-- <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div> --}}
                   @else

                   <div class="pagetitle">
                    <h1>{{trans('lang.dashboard')}}</h1>
                    <nav>
                      <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.html">{{trans('lang.home')}}</a></li>
                        <li class="breadcrumb-item active">{{trans('lang.dashboard')}}</li>
                      </ol>
                    </nav>
                  </div>
              
                  <section class="section dashboard">
                    <div class="row">
              
                      <!-- Left side columns -->
                      <div class="col-lg-12">
                        <div class="row">
              
                          <!-- Sales Card -->
                          <div class="col-xxl-4 col-md-6">
                            <div class="card info-card sales-card">
              
                              <div class="filter">
                                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                  <li class="dropdown-header text-start">
                                    <h6>{{trans('lang.filters')}}</h6>
                                  </li>
              
                                  <li><a class="dropdown-item" href="#">{{trans('lang.today')}}</a></li>
                                  <li><a class="dropdown-item" href="#">{{trans('lang.this_month')}}</a></li>
                                  <li><a class="dropdown-item" href="#">{{trans('lang.this_year')}}</a></li>
                                </ul>
                              </div>
              
                              <div class="card-body">
                                <h5 class="card-title">{{trans('lang.sales')}} <span>| {{trans('lang.today')}}</span></h5>
              
                                <div class="d-flex align-items-center">
                                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-cart"></i>
                                  </div>
                                  <div class="ps-3">
                                    <h6> <?= round($today_total, 2) ?>  {{trans('lang.ryal')}}</h6>
                                    <!-- <span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->
              
                                  </div>
                                </div>
                              </div>
              
                            </div>
                          </div><!-- End Sales Card -->
              
                          <!-- Revenue Card -->
                          <div class="col-xxl-4 col-md-6">
                            <div class="card info-card revenue-card">
              
                              <div class="filter">
                                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                  <li class="dropdown-header text-start">
                                    <h6>{{trans('lang.filters')}}</h6>
                                  </li>
              
                                  <li><a class="dropdown-item" href="#">{{trans('lang.today')}}</a></li>
                                  <li><a class="dropdown-item" href="#">{{trans('lang.this_month')}}</a></li>
                                  <li><a class="dropdown-item" href="#">{{trans('lang.this_year')}}</a></li>
                                </ul>
                              </div>
              
                              <div class="card-body">
                                <h5 class="card-title">{{trans('lang.sales')}} <span>| {{trans('lang.this_month')}}</span></h5>
              
                                <div class="d-flex align-items-center">
                                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-currency-dollar"></i>
                                  </div>
                                  <div class="ps-3">
                                    <h6> <?= round($month_total, 2) ?>  {{trans('lang.ryal')}}</h6>
                                    <!-- <span class="text-success small pt-1 fw-bold">8%</span> <span class="text-muted small pt-2 ps-1">increase</span> -->
              
                                  </div>
                                </div>
                              </div>
              
                            </div>
                          </div><!-- End Revenue Card -->
              
                          <!-- Customers Card -->
                          <div class="col-xxl-4 col-xl-12">
              
                            <div class="card info-card customers-card">
              
                              <div class="filter">
                                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                  <li class="dropdown-header text-start">
                                    <h6>{{trans('lang.filters')}}</h6>
                                  </li>
              
                                  <li><a class="dropdown-item" href="#">{{trans('lang.today')}}</a></li>
                                  <li><a class="dropdown-item" href="#">{{trans('lang.this_month')}}</a></li>
                                  <li><a class="dropdown-item" href="#">{{trans('lang.this_year')}}</a></li>
                              </div>
              
                              <div class="card-body">
                                <h5 class="card-title">{{trans('lang.customers')}} <span>| {{trans('lang.this_year')}}</span></h5>
              
                                <div class="d-flex align-items-center">
                                  <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-people"></i>
                                  </div>
                                  <div class="ps-3">
                                    <h6> <?= $customers ?></h6>
                                    <!-- <span class="text-danger small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">decrease</span> -->
              
                                  </div>
                                </div>
              
                              </div>
                            </div>
              
                          </div><!-- End Customers Card -->
              
                          <!-- Reports -->
                          <div class="col-12">
                            <div class="card">
              
                              <div class="filter">
                                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                  <li class="dropdown-header text-start">
                                    <h6>{{trans('lang.filters')}}</h6>
                                  </li>
              
                                  <li><a class="dropdown-item" href="#">{{trans('lang.today')}}</a></li>
                                  <li><a class="dropdown-item" href="#">{{trans('lang.this_month')}}</a></li>
                                  <li><a class="dropdown-item" href="#">{{trans('lang.this_year')}}</a></li>
                                </ul>
                              </div>
              
                              <div class="card-body">
                                <h5 class="card-title">{{trans('lang.reports')}} <span>/{{trans('lang.today')}}</span></h5>
              
                                <!-- Line Chart -->
                                <div id="reportsChart"></div>
                                <?php 
                                  $sellers = implode(',',array_column($reports, 'seller_id'));
                                  $totals = implode(',',array_column($reports, 'total_sales'));
                                ?>
                                <script>
                                  document.addEventListener("DOMContentLoaded", () => {
                                    new ApexCharts(document.querySelector("#reportsChart"), {
                                      series: [{
                                        name: '{{trans("lang.sales")}}',
                                        data: [<?= $totals ?>],
                                      }, {
                                        name: '{{trans("lang.sellers")}}',
                                        data: [<?= $sellers ?>]
                                      }],
                                      chart: {
                                        height: 350,
                                        type: 'area',
                                        toolbar: {
                                          show: true
                                        },
                                      },
                                      markers: {
                                        size: 4
                                      },
                                      colors: ['#4154f1', '#2eca6a', '#ff771d'],
                                      fill: {
                                        type: "gradient",
                                        gradient: {
                                          shadeIntensity: 1,
                                          opacityFrom: 0.3,
                                          opacityTo: 0.4,
                                          stops: [0, 900, 1000]
                                        }
                                      },
                                      dataLabels: {
                                        enabled: false
                                      },
                                      stroke: {
                                        curve: 'smooth',
                                        width: 2
                                      },
                                      xaxis: {
                                        type: 'datetime',
                                        categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z", "2018-09-19T02:30:00.000Z", "2018-09-19T03:30:00.000Z", "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z", "2018-09-19T06:30:00.000Z"]
                                      },
                                      tooltip: {
                                        x: {
                                          format: 'dd/MM/yy HH:mm'
                                        },
                                      }
                                    }).render();
                                  });
                                </script>
                                <!-- End Line Chart -->
              
                              </div>
              
                            </div>
                          </div><!-- End Reports -->
              
                          <!-- Recent Sales -->
                          <div class="col-12">
                            <div class="card recent-sales overflow-auto">
              
                              <div class="filter">
                                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                  <li class="dropdown-header text-start">
                                    <h6>{{trans('lang.filters')}}</h6>
                                  </li>
              
                                  <li><a class="dropdown-item" href="#">{{trans('lang.today')}}</a></li>
                                  <li><a class="dropdown-item" href="#">{{trans('lang.this_month')}}</a></li>
                                  <li><a class="dropdown-item" href="#">{{trans('lang.this_year')}}</a></li>
                                </ul>
                              </div>
              
                              <div class="card-body">
                                <h5 class="card-title">{{trans('lang.recent_sales')}} <span>| {{trans('lang.today')}}</span></h5>
              
                                <table class="table table-borderless">
                                  <thead>
                                    <tr>
                                      <th scope="col">#</th>
                                      <th scope="col">{{trans('lang.customers')}}</th>
                                      <th scope="col">{{trans('lang.sellers')}}</th>
                                      <th scope="col">{{trans('lang.price')}}</th>
                                      <th scope="col">{{trans('lang.status')}}</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                  <?php if(is_array($recent_orders) && count($recent_orders) > 0) {
                                      foreach($recent_orders as $k => $row1)
                                      {
                                        echo '
                                        <tr>
                                          <th scope="row">'.($k+1).'</th>
                                          <td>'.$row1['user_name'].'</td>
                                          <td>'.$row1['seller_name'].'</td>
                                          <td>'.$row1['total'].'</td>
                                          <td>'.($row1['status'] == 0 ? '<span class="badge bg-warning">'.trans('lang.pending').'</span>' : ($row1['status'] == 1 ? '<span class="badge bg-success">'.trans('lang.complete').'</span>' : ($row1['status'] == 2 ? '<span class="badge bg-primary">'.trans('lang.processing').'</span>' : '<span class="badge bg-danger">'.trans('lang.cancelled').'</span>'))).'</td>
                                        </tr>';
                                      }
                                    } ?>
                                  </tbody>
                                </table>
              
                              </div>
              
                            </div>
                          </div><!-- End Recent Sales -->
              
                          <!-- Top Selling -->
                          <div class="col-12">
                            <div class="card top-selling overflow-auto">
              
                              <div class="filter">
                                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                  <li class="dropdown-header text-start">
                                    <h6>{{trans('lang.filters')}}</h6>
                                  </li>
              
                                  <li><a class="dropdown-item" href="#">{{trans('lang.today')}}</a></li>
                                  <li><a class="dropdown-item" href="#">{{trans('lang.this_month')}}</a></li>
                                  <li><a class="dropdown-item" href="#">{{trans('lang.this_year')}}</a></li>
                                </ul>
                              </div>
              
                              <div class="card-body pb-0">
                                <h5 class="card-title">{{trans('lang.top_sellings')}} <span>| {{trans('lang.today')}}</span></h5>
              
                                <table class="table table-borderless">
                                  <thead>
                                    <tr>
                                      <th scope="col">#</th>
                                      <th scope="col">{{trans('lang.product')}}</th>
                                      <th scope="col">{{trans('lang.price')}}</th>
                                      <th scope="col">{{trans('lang.sold')}}</th>
                                      <th scope="col">{{trans('lang.total_sale')}}</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php if(is_array($top_sellings) && count($top_sellings) > 0) {
                                            foreach($top_sellings as $key => $row) {
                                              if(is_array($row)){
                                                echo '
                                                <tr>
                                                  <th scope="row">'.($key+1).'</th>
                                                  <td><a href="'.route('product.index').'" class="text-primary fw-bold">'.$row['product_name'].'</a></td>
                                                  <td>'.$row['item_price'].'</td>
                                                  <td class="fw-bold">'.$row['total_qty'].'</td>
                                                  <td>'.$row['item_total'].'</td>
                                                </tr>';
                                              }
                                            }
                                    } ?>
                                  </tbody>
                                </table>
              
                              </div>
              
                            </div>
                          </div><!-- End Top Selling -->
              
                        </div>
                      </div><!-- End Left side columns -->
              
                      <!-- Right side columns -->
                      <div class="col-lg-4 d-none">
              
                        <!-- Recent Activity -->
                        <div class="card">
                          <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                              <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                              </li>
              
                              <li><a class="dropdown-item" href="#">Today</a></li>
                              <li><a class="dropdown-item" href="#">This Month</a></li>
                              <li><a class="dropdown-item" href="#">This Year</a></li>
                            </ul>
                          </div>
              
                          <div class="card-body">
                            <h5 class="card-title">Recent Activity <span>| Today</span></h5>
              
                            <div class="activity">
              
                              <div class="activity-item d-flex">
                                <div class="activite-label">32 min</div>
                                <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                                <div class="activity-content">
                                  Quia quae rerum <a href="#" class="fw-bold text-dark">explicabo officiis</a> beatae
                                </div>
                              </div><!-- End activity item-->
              
                              <div class="activity-item d-flex">
                                <div class="activite-label">56 min</div>
                                <i class='bi bi-circle-fill activity-badge text-danger align-self-start'></i>
                                <div class="activity-content">
                                  Voluptatem blanditiis blanditiis eveniet
                                </div>
                              </div><!-- End activity item-->
              
                              <div class="activity-item d-flex">
                                <div class="activite-label">2 hrs</div>
                                <i class='bi bi-circle-fill activity-badge text-primary align-self-start'></i>
                                <div class="activity-content">
                                  Voluptates corrupti molestias voluptatem
                                </div>
                              </div><!-- End activity item-->
              
                              <div class="activity-item d-flex">
                                <div class="activite-label">1 day</div>
                                <i class='bi bi-circle-fill activity-badge text-info align-self-start'></i>
                                <div class="activity-content">
                                  Tempore autem saepe <a href="#" class="fw-bold text-dark">occaecati voluptatem</a> tempore
                                </div>
                              </div><!-- End activity item-->
              
                              <div class="activity-item d-flex">
                                <div class="activite-label">2 days</div>
                                <i class='bi bi-circle-fill activity-badge text-warning align-self-start'></i>
                                <div class="activity-content">
                                  Est sit eum reiciendis exercitationem
                                </div>
                              </div><!-- End activity item-->
              
                              <div class="activity-item d-flex">
                                <div class="activite-label">4 weeks</div>
                                <i class='bi bi-circle-fill activity-badge text-muted align-self-start'></i>
                                <div class="activity-content">
                                  Dicta dolorem harum nulla eius. Ut quidem quidem sit quas
                                </div>
                              </div><!-- End activity item-->
              
                            </div>
              
                          </div>
                        </div><!-- End Recent Activity -->
              
                        <!-- Budget Report -->
                        <div class="card">
                          <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                              <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                              </li>
              
                              <li><a class="dropdown-item" href="#">Today</a></li>
                              <li><a class="dropdown-item" href="#">This Month</a></li>
                              <li><a class="dropdown-item" href="#">This Year</a></li>
                            </ul>
                          </div>
              
                          <div class="card-body pb-0">
                            <h5 class="card-title">Budget Report <span>| This Month</span></h5>
              
                            <div id="budgetChart" style="min-height: 400px;" class="echart"></div>
              
                            <script>
                              document.addEventListener("DOMContentLoaded", () => {
                                var budgetChart = echarts.init(document.querySelector("#budgetChart")).setOption({
                                  legend: {
                                    data: ['Allocated Budget', 'Actual Spending']
                                  },
                                  radar: {
                                    // shape: 'circle',
                                    indicator: [{
                                        name: 'Sales',
                                        max: 6500
                                      },
                                      {
                                        name: 'Administration',
                                        max: 16000
                                      },
                                      {
                                        name: 'Information Technology',
                                        max: 30000
                                      },
                                      {
                                        name: 'Customer Support',
                                        max: 38000
                                      },
                                      {
                                        name: 'Development',
                                        max: 52000
                                      },
                                      {
                                        name: 'Marketing',
                                        max: 25000
                                      }
                                    ]
                                  },
                                  series: [{
                                    name: 'Budget vs spending',
                                    type: 'radar',
                                    data: [{
                                        value: [4200, 3000, 20000, 35000, 50000, 18000],
                                        name: 'Allocated Budget'
                                      },
                                      {
                                        value: [5000, 14000, 28000, 26000, 42000, 21000],
                                        name: 'Actual Spending'
                                      }
                                    ]
                                  }]
                                });
                              });
                            </script>
              
                          </div>
                        </div><!-- End Budget Report -->
              
                        <!-- Website Traffic -->
                        <div class="card">
                          <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                              <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                              </li>
              
                              <li><a class="dropdown-item" href="#">Today</a></li>
                              <li><a class="dropdown-item" href="#">This Month</a></li>
                              <li><a class="dropdown-item" href="#">This Year</a></li>
                            </ul>
                          </div>
              
                          <div class="card-body pb-0">
                            <h5 class="card-title">Website Traffic <span>| Today</span></h5>
              
                            <div id="trafficChart" style="min-height: 400px;" class="echart"></div>
              
                            <script>
                              document.addEventListener("DOMContentLoaded", () => {
                                echarts.init(document.querySelector("#trafficChart")).setOption({
                                  tooltip: {
                                    trigger: 'item'
                                  },
                                  legend: {
                                    top: '5%',
                                    left: 'center'
                                  },
                                  series: [{
                                    name: 'Access From',
                                    type: 'pie',
                                    radius: ['40%', '70%'],
                                    avoidLabelOverlap: false,
                                    label: {
                                      show: false,
                                      position: 'center'
                                    },
                                    emphasis: {
                                      label: {
                                        show: true,
                                        fontSize: '18',
                                        fontWeight: 'bold'
                                      }
                                    },
                                    labelLine: {
                                      show: false
                                    },
                                    data: [{
                                        value: 1048,
                                        name: 'Search Engine'
                                      },
                                      {
                                        value: 735,
                                        name: 'Direct'
                                      },
                                      {
                                        value: 580,
                                        name: 'Email'
                                      },
                                      {
                                        value: 484,
                                        name: 'Union Ads'
                                      },
                                      {
                                        value: 300,
                                        name: 'Video Ads'
                                      }
                                    ]
                                  }]
                                });
                              });
                            </script>
              
                          </div>
                        </div><!-- End Website Traffic -->
              
                        <!-- News & Updates Traffic -->
                        <div class="card">
                          <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                              <li class="dropdown-header text-start">
                                <h6>Filter</h6>
                              </li>
              
                              <li><a class="dropdown-item" href="#">Today</a></li>
                              <li><a class="dropdown-item" href="#">This Month</a></li>
                              <li><a class="dropdown-item" href="#">This Year</a></li>
                            </ul>
                          </div>
              
                          <div class="card-body pb-0">
                            <h5 class="card-title">News &amp; Updates <span>| Today</span></h5>
              
                            <div class="news">
                              <div class="post-item clearfix">
                                <img src="assets/img/news-1.jpg" alt="">
                                <h4><a href="#">Nihil blanditiis at in nihil autem</a></h4>
                                <p>Sit recusandae non aspernatur laboriosam. Quia enim eligendi sed ut harum...</p>
                              </div>
              
                              <div class="post-item clearfix">
                                <img src="assets/img/news-2.jpg" alt="">
                                <h4><a href="#">Quidem autem et impedit</a></h4>
                                <p>Illo nemo neque maiores vitae officiis cum eum turos elan dries werona nande...</p>
                              </div>
              
                              <div class="post-item clearfix">
                                <img src="assets/img/news-3.jpg" alt="">
                                <h4><a href="#">Id quia et et ut maxime similique occaecati ut</a></h4>
                                <p>Fugiat voluptas vero eaque accusantium eos. Consequuntur sed ipsam et totam...</p>
                              </div>
              
                              <div class="post-item clearfix">
                                <img src="assets/img/news-4.jpg" alt="">
                                <h4><a href="#">Laborum corporis quo dara net para</a></h4>
                                <p>Qui enim quia optio. Eligendi aut asperiores enim repellendusvel rerum cuder...</p>
                              </div>
              
                              <div class="post-item clearfix">
                                <img src="assets/img/news-5.jpg" alt="">
                                <h4><a href="#">Et dolores corrupti quae illo quod dolor</a></h4>
                                <p>Odit ut eveniet modi reiciendis. Atque cupiditate libero beatae dignissimos eius...</p>
                              </div>
              
                            </div><!-- End sidebar recent posts-->
              
                          </div>
                        </div><!-- End News & Updates -->
              
                      </div><!-- End Right side columns -->
              
                    </div>
                  </section>
                    @endif

                </div>
            </div>
        </div>

@endsection

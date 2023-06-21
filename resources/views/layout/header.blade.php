<div class="page-header">
    <div class="header-wrapper row m-0">
      <form class="form-inline search-full col" action="#" method="get">
        <div class="form-group w-100">
          <div class="Typeahead Typeahead--twitterUsers">
            <div class="u-posRelative">
              <input class="demo-input Typeahead-input form-control-plaintext w-100" type="text" placeholder="Search Koho .." name="q" title="" autofocus>
              <div class="spinner-border Typeahead-spinner" role="status"><span class="sr-only">Loading...</span></div><i class="close-search" data-feather="x"></i>
            </div>
            <div class="Typeahead-menu"></div>
          </div>
        </div>
      </form>
      <div class="header-logo-wrapper col-auto p-0">
        <div class="logo-wrapper"><a href="{{ route('dashboard') }}"><img class="img-fluid for-light" src="{{ asset('assets/images/logo/logo.png') }}" alt=""><img class="img-fluid for-dark" src="{{ asset('assets/images/logo/logo-dark.png') }}" alt=""></a></div>
        <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="align-center"></i></div>
      </div>
      <div class="left-header col horizontal-wrapper ps-0">
        <div class="input-group">
          <input class="form-control" type="text" placeholder="Search Here........"><span class="input-group-text mobile-search"><i data-feather="search"></i></span>
        </div>
      </div>
      <div class="nav-right col-6 pull-right right-header p-0">
        <ul class="nav-menus">
          <li class="language-nav">
            <div class="translate_wrapper">
              <div class="current_lang">
                <div class="lang"><i class="flag-icon flag-icon-us"></i><span class="lang-txt">EN                               </span></div>
              </div>
              <div class="more_lang">
                <div class="lang selected" data-value="en"><i class="flag-icon flag-icon-us"></i><span class="lang-txt">English<span> (US)</span></span></div>
                <div class="lang" data-value="de"><i class="flag-icon flag-icon-de"></i><span class="lang-txt">Deutsch</span></div>
                <div class="lang" data-value="es"><i class="flag-icon flag-icon-es"></i><span class="lang-txt">Español</span></div>
                <div class="lang" data-value="fr"><i class="flag-icon flag-icon-fr"></i><span class="lang-txt">Français</span></div>
                <div class="lang" data-value="pt"><i class="flag-icon flag-icon-pt"></i><span class="lang-txt">Português<span> (BR)</span></span></div>
                <div class="lang" data-value="cn"><i class="flag-icon flag-icon-cn"></i><span class="lang-txt">简体中文</span></div>
                <div class="lang" data-value="ae"><i class="flag-icon flag-icon-ae"></i><span class="lang-txt">لعربية <span> (ae)</span></span></div>
              </div>
            </div>
          </li>
          <li>
            <div class="mode"><i data-feather="moon"></i></div>
          </li>
          <li class="onhover-dropdown">
            <div class="notification-box"><i data-feather="star"></i></div>
            <div class="onhover-show-div bookmark-flip">
              <div class="flip-card">
                <div class="flip-card-inner">
                  <div class="front">
                    <ul class="droplet-dropdown bookmark-dropdown">
                      <li class="gradient-primary"><i data-feather="star"></i>
                        <h3 class="mb-0">Bookmark</h3>
                      </li>
                      <li>
                        <div class="row">
                          <div class="col-4 text-center"><a href="{{ route('dashboard') }}"><i data-feather="file-text"></i></a></div>
                          <div class="col-4 text-center"><a href="{{ route('dashboard') }}"><i data-feather="activity"></i></a></div>
                          <div class="col-4 text-center"><a href="{{ route('dashboard') }}"><i data-feather="users"></i></a></div>
                          <div class="col-4 text-center"><a href="{{ route('dashboard') }}"><i data-feather="clipboard"></i></a></div>
                          <div class="col-4 text-center"><a href="{{ route('dashboard') }}"><i data-feather="anchor"></i></a></div>
                          <div class="col-4 text-center"><a href="{{ route('dashboard') }}"><i data-feather="settings"></i></a></div>
                        </div>
                      </li>
                      <li class="text-center">
                        <button class="flip-btn" id="flip-btn">Add New Bookmark</button>
                      </li>
                    </ul>
                  </div>
                  <div class="back">
                    <ul>
                      <li>
                        <div class="droplet-dropdown bookmark-dropdown flip-back-content">
                          <input type="text" placeholder="search...">
                        </div>
                      </li>
                      <li class="pb-0">
                        <button class="d-block flip-back" id="flip-back">Back </button>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </li>
          <li class="onhover-dropdown">
            <div class="notification-box"><i data-feather="bell"></i><span class="badge rounded-pill badge-primary">4                                </span></div>
            <ul class="notification-dropdown onhover-show-div">
              <li><i data-feather="bell">            </i>
                <h3 class="mb-0">Notifications</h3>
              </li>
              <li><a href="{{ route('dashboard')}}">
                  <p><i class="fa fa-circle-o me-3 font-primary"> </i>Delivery processing <span class="pull-right">10 min.</span></p></a></li>
              <li><a href="{{ route('dashboard')}}">
                  <p><i class="fa fa-circle-o me-3 font-success"></i>Order Complete<span class="pull-right">1 hr</span></p></a></li>
              <li><a href="{{ route('dashboard')}}">
                  <p><i class="fa fa-circle-o me-3 font-info"></i>Tickets Generated<span class="pull-right">3 hr</span></p></a></li>
              <li><a href="{{ route('dashboard')}}">
                  <p><i class="fa fa-circle-o me-3 font-danger"></i>Delivery Complete<span class="pull-right">6 hr</span></p></a></li>
              <li><a class="btn btn-primary" href="{{ route('dashboard')}}">Check all notification</a></li>
            </ul>
          </li>
          <li class="onhover-dropdown"><i data-feather="message-square"></i>
            <ul class="chat-dropdown onhover-show-div">
              <li><i data-feather="message-square"></i>
                <h3 class="mb-0">Message Box</h3>
              </li>
              <li>
                <div class="d-flex"><img class="img-fluid rounded-circle me-3" src="{{ asset('assets/images/user/6.jpg') }}" alt="">
                  <div class="status-circle online"></div>
                  <div class="flex-grow-1"><a href="{{ route('dashboard')}}"><span>Erica Hughes</span>
                      <p>Do you want to go see movie?</p></a></div>
                  <p class="f-12 font-success">2 mins ago</p>
                </div>
              </li>
              <li>
                <div class="d-flex"><img class="img-fluid rounded-circle me-3" src="{{ asset('assets/images/user/1.jpg') }}" alt="">
                  <div class="status-circle online"></div>
                  <div class="flex-grow-1"><a href="{{ route('dashboard')}}"><span>Kori Thomas</span>
                      <p>Thank you for rating us.</p></a></div>
                  <p class="f-12 font-success">5 mins ago</p>
                </div>
              </li>
              <li>
                <div class="d-flex"><img class="img-fluid rounded-circle me-3" src="{{ asset('assets/images/user/7.jpg') }}" alt="">
                  <div class="status-circle offline"></div>
                  <div class="flex-grow-1"><a href="{{ route('dashboard')}}"><span>Ain Chavez</span>
                      <p>What`s the project report update?</p></a></div>
                  <p class="f-12 font-danger">9 mins ago</p>
                </div>
              </li>
              <li class="text-center"> <a class="btn btn-primary" href="{{ route('dashboard')}}">View All     </a></li>
            </ul>
          </li>
          <li class="maximize"><a class="text-dark" href="#!" onclick="javascript:toggleFullScreen()"><i data-feather="maximize"></i></a></li>
          <li class="profile-nav onhover-dropdown p-0 me-0">
            <div class="d-flex profile-media"><img class="b-r-50" src="{{ asset('assets/images/dashboard/profile.png') }}" alt="">
              <div class="flex-grow-1"><span>{{ucfirst(auth()->user()->name)}}</span>
                <p class="mb-0 font-roboto">{{auth()->user()->getRoleNames()->first();}} <i class="middle fa fa-angle-down"></i></p>
              </div>
            </div>
            <ul class="profile-dropdown onhover-show-div">
              <li><a href="{{ route('dashboard')}}"><i data-feather="user"></i><span>Account </span></a></li>
              <li><a href="{{ route('dashboard')}}"><i data-feather="mail"></i><span>Inbox</span></a></li>
              <li><a href="{{ route('dashboard')}}"><i data-feather="file-text"></i><span>Taskboard</span></a></li>
              <li><a href="{{ route('login.logout')}}"><i data-feather="log-in"> </i><span>Log in</span></a></li>
            </ul>
          </li>
        </ul>
      </div>
      <script class="result-template" type="text/x-handlebars-template">
        <div class="ProfileCard u-cf">
        <div class="ProfileCard-avatar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay m-0"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path><polygon points="12 15 17 21 7 21 12 15"></polygon></svg></div>
        <div class="ProfileCard-details">
        {{-- <div class="ProfileCard-realName">{{name}}</div> --}}
        </div>
        </div>
      </script>
      <script class="empty-template" type="text/x-handlebars-template"><div class="EmptyMessage">Your search turned up 0 results. This most likely means the backend is down, yikes!</div></script>
    </div>
  </div>

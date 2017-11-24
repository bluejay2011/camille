<nav class="navbar navbar-expand-lg navbar-light bg-light pt-3 pb-3">
    <a class="navbar-brand" href="/"><i class="fa fa-magic" aria-hidden="true"></i>{{ Config::get('app.name') }}</a>
    <button class="navbar-toggler d-lg-none" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>


    <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        @if (isset($q))
        <form class="form-inline col-7" style="margin-bottom: 0px" action="/search">
            <input class="form-control mr-sm-2 col-10" type="text" name="q" placeholder="Find Books, Journals, Exams, Authors and more..." aria-label="Search" value="{{ $q }}">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
        </form>
        <ul class="navbar-nav col-4 offset-4">
            <li class="nav-item">
                <a class="nav-link" href="/signin">Sign In</a>
            </li>
        </ul>
        @else
        <ul class="navbar-nav col-12 offset-11">
            <li class="nav-item">
                <a class="nav-link" href="/signin">Sign In</a>
            </li>
        </ul>
        @endif

    </div>
</nav>
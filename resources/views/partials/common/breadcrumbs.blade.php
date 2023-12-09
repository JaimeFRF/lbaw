<nav class="m-3" aria-label="breadcrumb" id="breadcrumbs" >
  <ol class="breadcrumb">

    @foreach($breadcrumbs as $name => $path)
      <li class="breadcrumb-item"><a href="{{$path}}">{{$name}}</a></li>
    @endforeach

    @if($current != null)
      <li class="breadcrumb-item" aria-current="page">{{$current}}</li>
    @endif
  </ol>
</nav>
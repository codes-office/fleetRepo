@extends("layouts.app")
@section("breadcrumb")
<li class="breadcrumb-item active">@lang('fleet.reviews')</li>
@endsection
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card card-info">
      <div class="card-header">
        <h3 class="card-title">
          @lang('fleet.reviews')
        </h3>
      </div>

      <div class="card-body table-responsive">
        <table class="table" id="data_table">
          <thead class="thead-inverse">
            <tr>
              <th>@lang('fleet.user')</th>
              <th>@lang('fleet.booking_id')</th>
              <th>@lang('fleet.ratings')</th>
              <th>@lang('fleet.review')</th>
            </tr>
          </thead>
          <tbody>
          @foreach($reviews as $review)
            <tr>
              <td>{{$review->user->name}}</td>
              <td>{{$review->booking_id}}</td>
              <td>
              @php($flot=ltrim(($review->ratings - floor($review->ratings)),"0."))
              @for($i=1;$i<=$review->ratings;$i++)
              <i class="fa fa-star"></i>
              @endfor
              @if($flot>0 && $review->ratings<5)
              <i class="fa fa-star-half"></i>
              @endif
              </td>
              <td>{{$review->review_text}}</td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
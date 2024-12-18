@extends('layouts.app')
@section('extra_css')
<style type="text/css">
	table#acrylic {
            border-collapse: separate;
            background: #fff;
            -moz-border-radius: 5px;
            -webkit-border-radius: 5px;
            border-radius: 5px;
            margin: 50px auto;
            -moz-box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
            -webkit-box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
            cursor: pointer;
        }

        #acrylic thead {
            -moz-border-radius: 5px;
            -webkit-border-radius: 5px;
            border-radius: 5px;
        }

        #acrylic thead th {
            font-family: 'Roboto';
            font-size: 16px;
            font-weight: 400;
            color: #fff;
            text-shadow: 1px 1px 0px rgba(0, 0, 0, 0.5);
            text-align: left;
            padding: 20px;
            background-size: 100%;
            background-image: -webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #646f7f), color-stop(100%, #4a5564));
            background-image: -moz-linear-gradient(#646f7f, #4a5564);
            background-image: -webkit-linear-gradient(#646f7f, #4a5564);
            background-image: linear-gradient(#646f7f, #4a5564);
            border-top: 1px solid #858d99;
        }

        #acrylic thead th:first-child {
            -moz-border-top-right-radius: 5px;
            -webkit-border-top-left-radius: 5px;
            border-top-left-radius: 5px;
        }

        #acrylic thead th:last-child {
            -moz-border-top-right-radius: 5px;
            -webkit-border-top-right-radius: 5px;
            border-top-right-radius: 5px;
        }

        #acrylic tbody tr td {
            font-family: 'Open Sans', sans-serif;
            font-weight: 400;
            color: #5f6062;
            font-size: 13px;
            padding: 20px 20px 20px 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        #acrylic tbody tr:nth-child(2n) {
            background: #f0f3f5;
        }

        #acrylic tbody tr:last-child td {
            border-bottom: none;
        }

        #acrylic tbody tr:last-child td:first-child {
            -moz-border-bottom-right-radius: 5px;
            -webkit-border-bottom-left-radius: 5px;
            border-bottom-left-radius: 5px;
        }

        #acrylic tbody tr:last-child td:last-child {
            -moz-border-bottom-right-radius: 5px;
            -webkit-border-bottom-right-radius: 5px;
            border-bottom-right-radius: 5px;
        }

</style>
@endsection
@section('content')
<div class="wrapper">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
        	<table id="acrylic" width="95%">
        		<thead>
        		    <tr>
        		        <th><h4 class="text-center text-bold">{{Auth::user()->name}}</h4></th>
        		    </tr>
        		</thead>
        		<tbody>
        		    <tr>
        		        <td><h5 class="text-center text-bold">@lang('fleet.total') @lang('fleet.amount') : {{Hyvikk::get('currency')}} {{(is_null($income[0]->income) ? 0 : $income[0]->income)}}
        		        </h5></td>
        		    </tr>
        		    <tr>
        		        <td><h5 class="text-center text-bold">
        		        	@lang('fleet.total') @lang('fleet.distence') : {{(is_null($total_kms[0]->total_kms) ? 0 : $total_kms[0]->total_kms)}} {{Hyvikk::get('dis_format')}}
        		        </h5></td>
        		    </tr>
        		    <tr>
        		        <td><h5 class="text-center text-bold">
        		        	@lang('fleet.total') @lang('fleet.waitingtime') : {{$time}}
        		        </h5></td>
        		    </tr>
        		    <tr>
        		        <td><h5 class="text-center text-bold">
        		        	@lang('fleet.travel_time') : {{$travel_time}} Minutes
        		        </h5></td>
        		    </tr>
        		</tbody>
        	</table>
        </div>
    </div>
</div>
@endsection
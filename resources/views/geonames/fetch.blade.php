@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">


                <div class="card">
                    <div class="card-header">
                        GeoNames
                    </div>
                    <div class="card-body">

                        <form action="{{ route('geoname.fetch') }}" method="post">
                            @csrf

                            <div class="form-group">
                                <label>Country Code</label>
                                <input type="text" name="country-code" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>

                    </div>
                </div>


            </div>
        </div>
    </div>

@endsection

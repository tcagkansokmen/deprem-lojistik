{{-- Extends layout --}}
@extends('layout.default')

{{-- Content --}}
@section('content')

    <style>
        #vehicles_wrapper .text-left{
            display:flex;
        }
        .filter-wrapper{
            display:flex;
            flex-direction:row;
        }
        @media(max-width:550px){
            .filter-wrapper{
                display:flex;
                flex-direction:column;
                margin-top:15px;
                border-top:1px solid #ddd;
                padding-top:15px;
            }
        }
    </style>
    <div class="home-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <form action="/" method="GET">
                        <x-honeypot />
                        <div class="filter-wrapper" >
                            <div class="form-group" style="margin-right:20px;">
                                <label for="">Varış Yerine Göre</label>
                                <select name="to" id="" class="form-control">
                                    <option value="">Seçiniz</option>
                                    @foreach($arriving as $city)
                                        <option value="{{ $city }}" {{ Request::get('to') == $city ? 'selected' : null  }}>{{ $city }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" style="margin-right:20px;">
                                <label for="">Yardım Türüne Göre</label>
                                <select name="category_id" id="" class="form-control">
                                    <option value="">Seçiniz</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ Request::get('category_id') == $category->id ? 'selected' : null  }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" style="margin-right:20px;">
                                <label for="">Statüsüne Göre</label>
                                <select name="status" id="" class="form-control">
                                    <option value="">Seçiniz</option>
                                    <option value="is_arrived" {{ Request::get('status') == 'is_arrived' ? 'selected' : null  }}>Yardım Ulaştı/Bekliyor</option>
                                    <option value="is_done" {{ Request::get('status') == 'is_done' ? 'selected' : null  }}>Yardım Tamamlandı</option>
                                    <option value="on_road" {{ Request::get('status') == 'on_road' ? 'selected' : null  }}>Yardım Yolda</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-right:20px;">
                                <label for="">Plaka/İsme Göre</label>
                                <input type="text" name="name" class="form-control" value="{{ Request::get('name') }}">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-light-success" style="margin-top:23px;">Filtrele</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="home-wrapper-white">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 mt-10 table-responsive" style="margin-top:35px;">
                    <table class="table table-striped table-hover table-responsive table-checkable mt-3" id="vehicles"></table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="vehicleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('vehicle-form') }}" method="POST" class="general-form">
                    <x-honeypot />
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Yeni Tedarik Bilgisi Ekle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <h5>İletişim</h5>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="plate" class="form-label">Araç Plakası/Yardım Bilgisi (*)</label>
                                <input type="text" class="form-control" id="plate" name="name" placeholder="Lütfen yardım yüklü aracın plakasını girin. Yönlendirme süreçleri için kullanılacaktır.">
                            </div>
                            <div class="col-6">
                                <label for="contact_name" class="form-label">İletişim Kişi (*)</label>
                                <input type="text" class="form-control" id="contact_name" name="contact_name" placeholder="İletişime geçilebilecek kişi (Şoför, yetkili vb.)">
                            </div>
                            <div class="col-6">
                                <label for="contact_phone" class="form-label">İletişim Telefon (*)</label>
                                <input type="text" class="form-control phone" name="contact_phone" id="contact_phone" placeholder="İletişime geçilecek telefon">
                            </div>
                            <hr class="mt-5">
                            <div class="col-sm-12">
                                <h5>Ulaşım</h5>
                            </div>
                            <div class="col-6">
                                <label for="from" class="form-label">Yola Çıkılan Kent (*)</label>
                                <select name="from" id="" class="form-control">
                                    @foreach($cities as $city)
                                        <option value="{{ $city }}">{{ $city }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="to" class="form-label">Varılacak Kent</label>
                                <select name="to" id="" class="form-control">
                                    @foreach($arriving as $city)
                                        <option value="{{ $city }}">{{ $city }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 mt-3">
                                <label for="start_at" class="form-label">Kalkış Tarih/Saat</label>
                                <input type="text" class="form-control datetime" id="start_at" name="start_at" placeholder="GG-AA-YYYY SS:DD" value="{{ date('d-m-Y H:i') }}">
                            </div>
                            <div class="col-6 mt-3">
                                <label for="end_at" class="form-label">Varış Tarih/Saat</label>
                                <input type="text" class="form-control datetime" name="end_at" id="end_at" placeholder="GG-AA-YYYY SS:DD">
                            </div>

                            <hr class="mt-3">
                            <div class="col-sm-12">
                                <h5>Yardım İçeriği</h5>
                            </div>

                            <div class="repeater">
                                <div data-repeater-list="contents">
                                    <div data-repeater-item class="mb-2">
                                        <div class="row">
                                            <div class="col-sm-10">
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <label for="category_id" class="form-label">Kategori</label>
                                                        <select name="category_id" id="" class="form-control">
                                                            @foreach($categories as $category)
                                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <label for="product" class="form-label">Ürün Adı</label>
                                                        <input type="text" class="form-control" id="product" name="product" placeholder="Ör: Çocuk kıyafeti">
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <label for="quantity" class="form-label">Miktar</label>
                                                        <input type="text" class="form-control" id="quantity" name="quantity" placeholder="Ör: 1000, 10, 100">
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <label for="unit" class="form-label">Birim (Koli, Kg vb.)</label>
                                                        <input type="text" class="form-control" id="unit" name="unit" placeholder="Ör: Koli, Ton, Adet">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <button class="btn btn-sm btn-danger" style="margin-top:25px;" data-repeater-delete type="button">Sil</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-dark btn-sm mt-5" data-repeater-create type="button">+ Yeni Kalem Ekle</button>
                            </div>
                            <p class="mt-2">Yukarıdaki bilgileri ne kadar çok doldurabilirseniz, yardımlar o kadar doğru koordine ederek ihtiyaç bölgelerine yönlendirilebilirler.</p>
                        </div>
                        <hr>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tedarik Detayları</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="supply-detail">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('vehicle-form-save') }}" method="POST" class="general-form">
                    <x-honeypot />
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Tedarik Detayları</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="supply-update">

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

{{-- Styles Section --}}
@section('styles')
@endsection


{{-- Scripts Section --}}
@section('scripts')
@endsection

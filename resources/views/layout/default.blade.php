<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8"/>

    {{-- Title Section --}}
    <title>{{ env('APP_NAME')  }}</title>

    {{-- Meta Data --}}
    <meta name="description" content="Deprem Yardım"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>

    {{-- Favicon --}}
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <link rel="stylesheet" href="//cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.4/css/buttons.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>


    <script src="https://cdn.datatables.net/buttons/2.3.4/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.html5.min.js"></script>
    <script src="ttps://cdn.datatables.net/buttons/2.3.4/js/buttons.print.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script src="js/repeater.js"></script>
    <script src="js/forms.js"></script>

    <link href="/css/web.css?v=1.1" rel="stylesheet">

    {{-- Includable CSS --}}
    @yield('styles')
</head>

<body class="" data-lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@include('layout.header')
@yield('content')
@include('layout.footer')

<script>
    $(document).ready(function(){
        $('.datetime').inputmask('99-99-9999 99:99');
        $('.phone').inputmask('(999) 999 99-99');
    });
</script>
<script>
$(document).ready(function () {
    $('.repeater').repeater({
        initEmpty: false,
        defaultValues: {
            'text-input': 'foo'
        },
        show: function () {
            $(this).slideDown();
        },
        hide: function (deleteElement) {
            if(confirm('Silmek istediğinize emin misiniz?')) {
                $(this).slideUp(deleteElement);
            }
        },
        ready: function (setIndexes) {
        },
    })
});
</script>
<script>

    var vehicleTable = $('#vehicles').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "deferRender": true,
        "ajax": "{{ route('vehicles-json') }}?{!! \Request::getQueryString() !!}",
        "dom": `<'row'<'col-sm-6 text-left'f><'col-sm-6 text-right'B>>
                <'row'<'col-sm-12'tr>>
                <'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 dataTables_pager'lp>>`,
        "pageLength": 50,
        "lengthMenu": [[50, 100, 250, 500, 1000], [50, 100, 250, 500, 1000]],
        "language": {
            "url":"https://cdn.datatables.net/plug-ins/1.10.20/i18n/Turkish.json"
        },
        "order": [[ 0, "desc" ]],
        "ordering": false,
        "columns": [
            { data: 'id', title: '#', name: 'id' },
            { data: 'name', title: "Araç Plakası", name: 'name', "render": function(data, type, row){
                return '<strong>' + row.name + '</strong>'
                } },
            { data: 'contact_name', name: 'contact_name', title: 'İletişim', "render": function (data, type, row) {
                    return row.contact_name + '<br>' + row.contact_phone
                }
            },
            { data: 'to', name: 'to', title: 'Nereye', visible: false },
            { data: 'from', name: 'from', title: 'Rota', "render": function (data, type, row) {
                return row.from + ' > ' + row.to
                }
            },
            { data: 'end_at', name: 'end_at', title: 'Varış Zamanı', visible: false },
            { data: 'start_at', name: 'start_at', title: 'Yola Çıkış / Varış', "render": function (data, type, row) {
                return row.start_at + ' > ' + row.end_at
                }
            },
            { data: 'id', name: 'id', title: 'Durum', searchable: false, "render": function (data, type, row) {
                    if(row.is_done){
                        return '<span class="btn btn-block btn-sm btn-light-success">Tamamlandı</span>'
                    }else if(row.is_arrived){
                        return '<span class="btn btn-block btn-sm btn-light-warning">Vardı Bekliyor</span>'
                    }else{
                        return '<span class="btn btn-block btn-sm btn-light-primary">Yolda</span>'
                    }
                }
            },

            { data: 'id', name: 'id', title: 'İşlem', searchable: false, "render": function (data, type, row) {
                    return '<button class="btn btn-dark detail-modal btn-sm" data-id="'+row.id+'">Detayları Gör</button>&nbsp;<button class="btn btn-light-info update-modal btn-sm" data-id="'+row.id+'">Güncelle</button>'
                }
            },
        ],
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });

</script>

<script>
    $("body").on('click', '.detail-modal', function(e){
        e.preventDefault()

        var id = $(this).attr('data-id')


        $.ajax({
            url: '/vehicle-detail/' + id,
            dataType: 'html',
            success: function(res) {
                $('.supply-detail').html(res)

                $("#detailModal").modal('show')
            },
        });
    });

    $("body").on('click', '.update-modal', function(e){
        e.preventDefault()

        var id = $(this).attr('data-id')


        $.ajax({
            url: '/vehicle-update/' + id,
            dataType: 'html',
            success: function(res) {
                $('.supply-update').html(res)

                $("#updateModal").modal('show')
            },
        });
    });

    $('.general-form').ajaxForm({
        beforeSubmit:  function(formData, jqForm, options){
            var val = null;
            $(".formprogress").show();
            $(".my-loader").addClass('active');
            $( ".required", jqForm ).each(function( index ) {
                if(!$(this).val()){
                    val = 1;
                    $(this).addClass('is-invalid').addClass('is-invalid').closest('.form-group').find('.invalid-feedback').show().html('');
                    $(this).closest('.form-group').find('.invalid-feedback').html("Bu alan zorunludur.");
                    $(this).closest('.form-group').addClass('invalid-select');
                }else{
                    $(this).removeClass('is-invalid');
                    $(this).closest('.form-group').removeClass('invalid-select');
                    $(this).closest('.form-group').find('.invalid-feedback').html(".");
                }
            });
            if(val){
                KTUtil.scrollTop();
            }
        },
        error: function(){
            $(".formprogress").hide();
            $(".my-loader").removeClass('active');
            Swal.fire({
                text: "Dikkat! Sistemsel bir hata nedeniyle kaydedilemedi!",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Tamam",
                customClass: {
                    confirmButton: "btn font-weight-bold btn-primary"
                }
            }).then(function() {
                KTUtil.scrollTop();
            });
        },
        dataType:  'json',
        success:   function(item){
            $(".my-loader").removeClass('active');
            $(".formprogress").hide();
            if(item.status){
                Swal.fire({
                    html: item.message,
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: "Tamam",
                    customClass: {
                        confirmButton: "btn font-weight-bold btn-primary"
                    }
                }).then(function() {
                    vehicleTable.ajax.reload();
                });
            }else{
                $('.is-invalid').removeClass('is-invalid').closest('.form-group').find('.invalid-feedback').hide();
                $('.is-invalid').removeClass('is-invalid').closest('.form-group').removeClass('.invalid-select');
                $.each(item.errors, function(key, value) {
                    $("[name="+key+"]").addClass('is-invalid').closest('.form-group').find('.invalid-feedback').show().html('');
                    $.each(value, function(k, v) {
                        $("[name="+key+"]").closest('.form-group').addClass('invalid-select').find('.invalid-feedback').append(v + "<br>");
                    });
                });

                Swal.fire({
                    html: item.message,
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Tamam",
                    customClass: {
                        confirmButton: "btn font-weight-bold btn-primary"
                    }
                }).then(function() {
                    KTUtil.scrollTop();
                });
            }
        }
    });


</script>
@yield('scripts')
</body>
</html>

 // "use strict";

// $(document).ready(function() {

    var is_user_logged_in = 0;
    var sedang_ujian = 0;

    // cek sudah login belum
    $.ajax({
        method: 'GET',
        url: site_url + '/login/data_onload',
        success: function(data) {
            var result = $.parseJSON(data);
            is_user_logged_in = result.is_user_logged_in;
            sedang_ujian      = result.sedang_ujian;
        },
        async: false
    });

    // panggil SyntaxHighlighter
    try {
        SyntaxHighlighter.all();
    } catch(e) {}

    // tooltip
    $('[data-toggle="tooltip"]').tooltip({html:true});

    // nivoslider login
    if ($("#slider-login").length) {
        $('#slider-login').nivoSlider();
    }

    // timeago
    $.timeago.settings.strings.suffixAgo = "yang lalu";
    $.timeago.settings.strings.seconds   = "kurang dari semenit";
    $.timeago.settings.strings.minute    = "sekitar semenit";
    $.timeago.settings.strings.minutes   = "%d menit";
    $.timeago.settings.strings.hour      = "sekitar sejam";
    $.timeago.settings.strings.hours     = "sekitar %d jam";
    $.timeago.settings.strings.day       = "satu hari";
    $.timeago.settings.strings.days      = "%d hari";
    $("time.timeago").timeago();

    // fungsi yang dipanggil saat ajax success
    function on_ajax_success(xhr)
    {
        // SyntaxHighlighter
        try {
            SyntaxHighlighter.highlight();
        } catch(e) {}

        // MathJax
        if (typeof MathJax !== 'undefined') {
            MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
        }

        // logout kalo session expired
        if (xhr.responseText == "403 Forbidden.") {
            location.href = site_url + '/login/sess_expired';
        }

        //timeago
        try {
            $("time.timeago").timeago();
        } catch(e) {}
    }

    // panggil fungsi setelah ajax success
    $(document).ajaxComplete(function( event, xhr, settings ) {
        on_ajax_success(xhr);
    });

    // area yang harus login dan tidak sedang ujian
    if (is_user_logged_in == 1 && sedang_ujian == 0) {

        // home
        if ($("#info-update-link").length) {
            // popup new version
            setTimeout(function() {
                var ada_update = 0;
                $.ajax({
                    type: "GET",
                    url: site_url + '/ajax/get_data/check_update',
                    success: function (data) {
                        ada_update = data;
                    },
                    async: false
                });

                if (ada_update == 1) {
                    $.colorbox({
                        href: site_url + "/welcome/new_version",
                        fixed: true,
                        width: 500,
                        onClosed : function() {
                            location.reload();
                        }
                    });
                } else {
                    url = $("#info-update-link").val();
                    $.ajax({
                        type: "GET",
                        url: document.location.protocol + '//ajax.googleapis.com/ajax/services/feed/load?v=1.0&num=20&callback=?&q=' + encodeURIComponent(url),
                        dataType: 'json',
                        success: function(xml){
                            values = xml.responseData.feed.entries;
                            var l = 1;
                            $.each( values, function( i, val ) {
                                if (l <= 15) {
                                    $("#info-update").append("<tr><td><a href='"+val.link+"' target='_blank'>"+val.title+"</a></td></tr>");
                                }
                                l++;
                            });
                        }
                    });
                }
            }, 1000);
        }

        // count new data
        function count_new_data()
        {
            $.ajax({
                method: "GET",
                url: site_url + '/ajax/get_data/count_new_data',
                success: function (data) {
                    var result = $.parseJSON(data);

                    // new msg
                    if (result.new_msg > 0) {
                        $(".menu-count-new-msg").html("");
                        $(".menu-count-new-msg").html('<b class="label orange pull-right">' + result.new_msg + '</b>');
                    } else {
                        $(".menu-count-new-msg").html("");
                    }

                    // new update
                    if (result.new_update > 0) {
                        $(".menu-count-new-update").html("");
                        $(".menu-count-new-update").html('<b class="label orange pull-right">' + result.new_update + '</b>');
                    } else {
                        $(".menu-count-new-update").html("");
                    }

                    // pending siswa
                    if (result.pending_siswa > 0) {
                        $(".menu-count-pending-siswa").html("");
                        $(".menu-count-pending-siswa").html('<b class="label orange pull-right">' + result.pending_siswa + '</b>');
                    } else {
                        $(".menu-count-pending-siswa").html("");
                    }

                    // pending pengajar
                    if (result.pending_pengajar > 0) {
                        $(".menu-count-pending-pengajar").html("");
                        $(".menu-count-pending-pengajar").html('<b class="label orange pull-right">' + result.pending_pengajar + '</b>');
                    } else {
                        $(".menu-count-pending-pengajar").html("");
                    }

                    // pending laporan
                    if (result.unread_laporan > 0) {
                        $(".menu-count-unread-laporan").html("");
                        $(".menu-count-unread-laporan").html('<b class="label orange pull-right">' + result.unread_laporan + '</b>');
                    } else {
                        $(".menu-count-unread-laporan").html("");
                    }

                    // last login
                    if (result.last_login_list) {
                        if ($("#show-last-login-list").length) {
                            $("#show-last-login-list").html(result.last_login_list);
                        }
                    }

                },
                async: false
            });
        }

        // panggil count new data
        count_new_data();

        // get new message di percakapan
        function get_new_msg()
        {
            if ($("#active_msg_id").length) {
                var active_msg_id = $("#active_msg_id").val();

                $.ajax({
                    method: 'POST',
                    url: site_url + '/ajax/post_data/new_msg',
                    data: 'active_msg_id=' + active_msg_id,
                    success: function(data) {
                        if (data != '') {
                            $('#list-msg > tbody:last-child').append(data);
                        }
                    },
                    async: false
                });
            }
        }

        setInterval(function() {
            count_new_data();

            get_new_msg();
        }, 10000);

        // jika ada class datatable
        if ($(".datatable").length) {
            $('.datatable').dataTable({
                "language": {
                    "url": base_url + "assets/comp/datatables/lang.id.json"
                },
                "aaSorting" : [],
                "bAutoWidth": false,
                "aoColumnDefs": [
                    { 'bSortable': false, 'aTargets': [ 0 ] }
                ],
                // "fnDrawCallback" : function( oSettings ) {

                // }
            });
        }

        // nestedSortable kelas
        if ($("ol#kelas").length) {
            $('ol#kelas').nestedSortable({
                forcePlaceholderSize: true,
                handle: 'div',
                helper: 'clone',
                items: 'li',
                // opacity: .6,
                placeholder: 'placeholder',
                // revert: 250,
                tabSize: 25,
                tolerance: 'pointer',
                toleranceElement: '> div',
                maxLevels: 2,
                isTree: true,
                expandOnHover: 700,
                startCollapsed: true
            });

            $('#update-hirarki').click(function(){
                $.ajax({
                    type : "POST",
                    url : site_url + "/ajax/post_data/hirarki_kelas",
                    data : $('ol.sortable').nestedSortable('serialize'),
                    success : function(data){
                        $('#response_update').html('<span class="text-success pull-right"><i class="icon icon-ok"></i> Update hirarki kelas berhasil</span>');
                        setTimeout(function(){
                            $('#response_update').html('');
                        },3000);
                    },
                    async: false
                });
            });
        }

        $('#kelas-mapel-kelas-parent-kelas').on('change', function() {
            $.ajax({
                type : "POST",
                url  : site_url + "/ajax/post_data/get_subkelas",
                data : "parent_kelas_id=" + this.value,
                success : function(data){
                    $('#kelas-mapel-kelas-sub-kelas').html(data);
                },
                async: false
            });
        });

        $('#kelas_id').change(function(){
            $.ajax({
                type : "POST",
                url  : site_url + "/ajax/post_data/mapel_kelas",
                data : "kelas_id=" + this.value,
                success : function(data){
                    $('#mapel_kelas_id').html(data);
                },
                async: false
            });
        });

        // cekall mapel
        $(".checkAll").change(function () {
            $(".checkbox-mapel").prop('checked', $(this).prop("checked"));
        });

        // colorbox pengajar
        if ($(".pengajar-iframe").length) {
            $(".pengajar-iframe").colorbox({
                iframe:true,
                width:"430",
                height:"405",
                fixed:true,
                onClosed : function() {
                    location.reload();
                }
            });
        }

        if ($(".pengajar-iframe-2").length) {
            $(".pengajar-iframe-2").colorbox({
                iframe:true,
                width:"400",
                height:"205",
                fixed:true,
                onClosed : function() {
                    location.reload();
                }
            });
        }

        if ($(".pengajar-iframe-3").length) {
            $(".pengajar-iframe-3").colorbox({
                iframe:true,
                width:"500",
                height:"305",
                fixed:true,
                onClosed : function() {
                    location.reload();
                }
            });
        }

        if ($(".pengajar-iframe-4").length) {
            $(".pengajar-iframe-4").colorbox({
                iframe:true,
                width:"600",
                height:"605",
                fixed:true,
                onClosed : function() {
                    location.reload();
                }
            });
        }

        if ($(".pengajar-iframe-5").length) {
            $(".pengajar-iframe-5").colorbox({
                iframe:true,
                width:"450",
                height:"340",
                fixed:true,
                onClosed : function() {
                    location.reload();
                }
            });
        }

        if ($(".pengajar-iframe-6").length) {
            $(".pengajar-iframe-6").colorbox({
                iframe:true,
                width:"430",
                height:"450",
                fixed:true,
                onClosed : function() {
                    location.reload();
                }
            });
        }

        if ($(".pengajar-iframe-7").length) {
            $(".pengajar-iframe-7").colorbox({
                iframe:true,
                width:"600",
                height:"500",
                fixed:true,
                onClosed : function() {
                    location.reload();
                }
            });
        }

        // colorbox siswa
        if ($(".siswa-iframe").length) {
            $(".siswa-iframe").colorbox({
                iframe:true,
                width:"400",
                height:"200",
                fixed:true,
                onClosed : function() {
                    location.reload();
                }
            });
        }

        if ($(".siswa-iframe-kelas-aktif").length) {
            $(".siswa-iframe-kelas-aktif").colorbox({
                iframe:true,
                width:"400",
                height:"200",
                fixed:true,
                onClosed : function() {
                    location.reload();
                }
            });
        }

        if ($(".siswa-iframe-2").length) {
            $(".siswa-iframe-2").colorbox({
                iframe:true,
                width:"400",
                height:"205",
                fixed:true,
                onClosed : function() {
                    location.reload();
                }
            });
        }

        if ($(".siswa-iframe-3").length) {
            $(".siswa-iframe-3").colorbox({
                iframe:true,
                width:"500",
                height:"305",
                fixed:true,
                onClosed : function() {
                    location.reload();
                }
            });
        }

        if ($(".siswa-iframe-4").length) {
            $(".siswa-iframe-4").colorbox({
                iframe:true,
                width:"600",
                height:"605",
                fixed:true,
                onClosed : function() {
                    location.reload();
                }
            });
        }

        if ($(".siswa-iframe-5").length) {
            $(".siswa-iframe-5").colorbox({
                iframe:true,
                width:"450",
                height:"340",
                fixed:true,
                onClosed : function() {
                    location.reload();
                }
            });
        }

        if ($("#siswa-edit-password").length) {
            $.colorbox({
                iframe:true,
                width:"500",
                height:"305",
                fixed:true,
                onClosed : function() {
                    location.href = site_url + '/login/pp';
                },
                href: $(".siswa-iframe-3").attr('href'),
                transition: "none"
            });
        }

        // materi
        if ($(".iframe-laporkan").length) {
            $(".iframe-laporkan").colorbox({
                iframe:true,
                width:"400",
                height:"300",
                fixed:true,
                overlayClose: true
            });
        }

        // pesan
        if ($('#msg-penerima').length) {
            $('#msg-penerima').tagsinput({
                allowDuplicates: false,
                typeahead: {
                    source: function(query) {
                        var results = [];
                        $.ajax({
                            method: "GET",
                            url: site_url + '/ajax/get_data/penerima?query=' + query,
                            success: function (data) {
                                results = $.parseJSON(data);
                            },
                            async: false
                        });

                        return results;
                    }
                },
                freeInput: false
            });
        }

        // filter pengajar
        function filter_pengajar_ch_uch_checkbox(source){
            checkboxes = document.getElementsByName('pengajar_id[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
              checkboxes[i].checked = source.checked;
            }
        }

        // pengumuman
        if ($("#pengumuman-tgl-tampil").length) {
            $('#pengumuman-tgl-tampil').dateRangePicker({
                language: 'id'
            });
        }

        // tambah siswa
        function username_default() {
            if (document.getElementById("default_username").checked) {
                var nis = $("#nis").val();
                if (nis == '') {
                    nis = new Date().getTime();
                }
                $("#username").val(nis + '@example.sch.id');
            } else {
                $("#username").val('');
            }
        }

        function username_default_pengajar() {
            if (document.getElementById("default_username").checked) {
                var nip = $("#nip").val();
                if (nip == '') {
                    nip = new Date().getTime();
                }
                $("#username").val(nip + '@example.sch.id');
            } else {
                $("#username").val('');
            }
        }

        // filter siswa
        function filter_siswa_ch_uch_checkbox(source){
            checkboxes = document.getElementsByName('siswa_id[]');
            for(var i=0, n=checkboxes.length;i<n;i++) {
              checkboxes[i].checked = source.checked;
            }
        }

        if ($(".iframe-lihat-nilai").length) {
            $(".iframe-lihat-nilai").colorbox({
                iframe:true,
                width:"400",
                height:"200",
                fixed:true,
            });
        }

        if ($(".iframe-pertanyaan, .iframe-pilihan").length) {
            $(".iframe-pertanyaan, .iframe-pilihan").colorbox({
                iframe:true,
                width:"800",
                height:"600",
                fixed:true,
                overlayClose: false,
                onClosed : function() {
                    location.reload();
                }
            });
        }

        if ($(".iframe-copy-pertanyaan").length) {
            $(".iframe-copy-pertanyaan").colorbox({
                iframe:true,
                width:"900",
                height:"600",
                fixed:true,
                overlayClose: false,
                onClosed : function() {
                    location.reload();
                }
            });
        }

        if ($(".iframe-koreksi-jawaban").length) {
            $(".iframe-koreksi-jawaban").colorbox({
                iframe:true,
                width:"700",
                height:"550",
                fixed:true,
                onClosed : function() {
                    location.reload();
                }
            });
        }

        if ($(".iframe-detail-jawaban").length) {
            $(".iframe-detail-jawaban").colorbox({
                iframe:true,
                width:"700",
                height:"600",
                fixed:true,
            });
        }

        if ($(".iframe-jawaban-sementara").length) {
            $(".iframe-jawaban-sementara").colorbox({
                iframe:true,
                width:"700",
                height:"600",
                fixed:true,
            });
        }

        $("#btn-perbaharui-aplikasi").on('click', function() {
            $("#progress-perbaharui").html('<img src="' + base_url + 'assets/images/loading.gif" style="width:30px;">');

            $.ajax({
                method: "GET",
                url: site_url + '/ajax/get_data/download_update',
                success: function (data) {
                    if (data == 0) {
                        $("#progress-perbaharui").html("Gagal mendownload file!");
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else if (data == 1) {
                        location.href = base_url + 'update-app.php';
                    } else {
                        $("#progress-perbaharui").html(data);
                    }
                }
            });
        });

    }

// });


$(document)
    .ready(function() {
        $('.ui.menu .ui.dropdown').dropdown({
            on: 'hover'
        });
        $('.ui.menu a.item')
            .on('click', function() {
                $(this)
                    .addClass('active')
                    .siblings()
                    .removeClass('active');
            });

        $('.ui.dropdown').dropdown({
            allowCategorySelection: true,
            forceSelection:false,

        });

        if (typeof nbrev !== 'undefined') {
            $('.revs').dropdown({
                allowCategorySelection: true,
                forceSelection:false,
                maxSelections : nbrev,
            });
        }


        $('.i-admin, .m-admin').hover(function() {
                $('.m-admin').show().siblings().hide();
            },
            function() {
                $('.m-admin').hide();
            }

        );

        $('.i-chair, .m-chair').hover(function() {
                $('.m-chair').show().siblings().hide();
            },
            function() {
                $('.m-chair').hide();
            }
        );

        $('.i-rev, .m-rev').hover(function() {
                $('.m-rev').show().siblings().hide();
            },
            function() {
                $('.m-rev').hide();
            }
        );

        $('.i-aut, .m-aut').hover(function() {
                $('.m-aut').show().siblings().hide();
            },
            function() {
                $('.m-aut').hide();
            }
        );



        var pos = $(".menu").width(); // don't need to use 'px'
        jQuery(".sub-menu").css({
            width: pos + 2
        }); // don't need escaping



        $(window).resize(function() {

            var pos = $(".menu").width(); // don't need to use 'px'
            jQuery(".sub-menu").css({
                width: pos + 2
            }); // don't need escaping

        });


        $('#soum, .soum').hover(function() {
            $('.soum').show().siblings().hide();
        });

        $('#select, .select').hover(function() {
            $('.select').show().siblings().hide();
        });

        $('#cam, .cam').hover(function() {
            $('.cam').show().siblings().hide();
        });


        $('#deadline_cam, #deadline_rev, #deadline_sub, #date_end, #date_start, #dateSlotEdit').calendar({
            type: 'date',
            monthFirst: true,
            formatter: {
                date: function(date, settings) {
                    if (!date) return '';
                    var day = date.getDate();
                    var month = date.getMonth() + 1;
                    var year = date.getFullYear();
                    return year + '-' + month + '-' + day;
                }
            }
        });

        $('.ui.radio.checkbox')
            .checkbox();

        $('.show-modal').click(function() {
            $('.ui.modal').modal('show');
        });

        $('.show-modal-long').click(function() {
            $('.long.modal').modal('show');
        });

        $('.ui.checkbox')
            .checkbox();

        $('#confTab').on("click", "#delete" , function(){
            $('#deleteConf').action = '/conferences/'+this.name+'/delete'
            //console.log(this);
            //console.log(this.name);
            $('.ui.delete.modal')
            .modal('show');
        });

        $('#confTab').on("click", "#create" , function(){
            $('#deleteConf').action = '/conferences/'+this.name+'/delete'
            //console.log(this);
            //console.log(this.name);
            $('#modal-create')
            .modal('show');

                $('#deadline_cam, #deadline_rev, #deadline_sub, #date_end, #date_start ').calendar({
                type: 'date',
                monthFirst: true,
                formatter: {
                    date: function(date, settings) {
                        if (!date) return '';
                        var day = date.getDate();
                        var month = date.getMonth() + 1;
                        var year = date.getFullYear();
                        return year + '-' + month + '-' + day;
                    }
                }
            });
        });

    });
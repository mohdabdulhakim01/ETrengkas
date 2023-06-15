<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div>Translate&nbsp;<span class="fas fa-language text-danger"></span></div>
                <div><button class="btn btn-floating btn-primary" onclick="toggleTerjemahan()"><span class="fas fa-eye"></span></button></div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4 col-sm-12 col-12">
                    <textarea name="" id="input-words" class="form-control" rows="15" onkeyup="semakAyat()" onkeydown="masukHuruf()"></textarea>
                </div>
                <div class="col-lg-8 col-sm-12 col-12">
                    <div id="ayat" class="row">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal-viewer" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-viewer-head">Upload Word</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-viewer-body">
            </div>
        </div>
    </div>
</div>
<div id="upload-word" class="d-none">
    <div class="d-flex justify-content-center align-items-center flex-column">
        <img src="" alt="" id="word-img" class="img-fluid">
        <h4 id="word-choice"></h4>
        <div id="upload-component" class="d-none gap-2">
            <div class="row">
                <div class="col-lg-9 col-sm-9 col-9">
                    <input type="file" name="word_choice" class="form-control">
                </div>
                <div class="col-lg-3 col-sm-3 col-3">
                    <button class="btn btn-primary"> Upload</button>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let lazytime = 0;
    let tungguRespon = true;

    function semakAyat() {

        tungguRespon = false;
    }

    function masukHuruf() {
        lazytime = 0;

    }

    function translated_template(found, value, source) {
        let unknown_source = 'unknown.png';
        let unknown_word = 'unknown';
        let column_length = 1;
        let word_length = value.split(' ').length;
        let fontsize = 10;
        let suggestUpload = `javascript:uploadWord('${value}')`;
        let wordInfo = `javascript:wordInfo('${value}','${source}')`;
        // if(word_length > 2){
        //     column_length = 3;
        // }else{
        //     column_length = 2;
        // }

        let html = `
            <div class="col-lg-${column_length} col-sm-${column_length} col-${column_length}">
              <div class="d-flex flex-column">
              <a href="${(found) ? wordInfo: suggestUpload }" title="${(found) ? value : unknown_word}" class="translated_text" >  
                <img src="public/dictionary_list/${(found) ? source : unknown_source}" class="img-fluid" style="max-width:100%!important;max-height:100%!important;" >
                   
                </a>
                <span style="font-size:${fontsize}px" class="translated-comment">${value}</span>
                </div>
            </div>
            `;
        return html;

    }

    async function semakTrengkas() {
        let inputWord = $("#input-words").val();
        let res = await axios.post(`trengkas/semak`, {
            "input_data": inputWord
        }).then((res) => {
            let output = res.data;
            let html_ = '';
            output.forEach((val) => {
                if (val.word == 'END_OF_DATA') {

                } else {

                    html_ += translated_template(val.found, val.word, val.source);
                }
            });
            $("#ayat").html(html_);
            console.log(output);
        });
    }


    function lazytimer() {
        // console.log('lazy timer loaded');
        setTimeout(() => {
            // console.log('waiting . . .');
            if (tungguRespon == false) {
                if (lazytime >= 200) {
                    console.log('semak bahasa');
                    tungguRespon = true;
                    semakTrengkas();
                }
                lazytime += 100;
            }
            lazytimer();
        }, 100);
    }
    let currentToggleStat = false;

    function toggleTerjemahan() {
        if (currentToggleStat) {
            // disable if true
            $(".translated-comment").removeClass('d-none');
            currentToggleStat = false;
        } else {
            // enable if false
            $(".translated-comment").addClass('d-none');
            currentToggleStat = true;
        }
    }

    function uploadWord(word) {
        viewModal('Muatnaik Perkataan', $("#upload-word").html());
        $("#modal-viewer-body #word-choice").html(word);
        $("#modal-viewer-body #upload-component").removeClass('d-none');
        //    console.log($("#modal-viewer-body").html());

    }

    function wordInfo(word, source) {
        viewModal('Lihat Ayat/Perkataan', $("#upload-word").html());
        $("#modal-viewer-body #word-choice").html(word);
        $("#modal-viewer-body #upload-component").addClass('d-none');
        $("#modal-viewer-body #word-img").attr('src', `public/dictionary_list/${source}`);
    }

    window.onload = function() {
        lazytimer();
    }

    function viewModal(label, body) {
        $("#modal-viewer-header").html(label);
        $("#modal-viewer-body").html(body);
        $("#modal-viewer").modal('show');
    }
</script>
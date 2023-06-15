<?php

namespace App\Http\Controllers;

use App\Models\Trengkas;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TrengkasController extends Controller
{
    public $list_data;
    public $ayat_sebenar;
    public $length_word_left;
    public $list_data_source = [];
    public $senarai_sumber = [];
    public function transparentImage(){
        $files = File::files(public_path('dictionary_list'));
        foreach ($files as $file) {
            $word = basename($file, '.png');
            $source_name = pathinfo($file, PATHINFO_FILENAME);
            echo 'file : '.$source_name.'.png transparented<br>';
            exec('convert "'.public_path('dictionary_list').'/'.$source_name.'.png" -fuzz 30% -transparent white "'.public_path('dictionary_list').'/'.$source_name.'.png"');
        }
    }
    public function export()
    {
        $files = File::files(public_path('dictionary_list'));
        foreach ($files as $file) {
            $word = basename($file, '.png');
            $source_name = pathinfo($file, PATHINFO_FILENAME);
            $author = 'admin';
            $date = date('Y-m-d');
            // word,source,created_at,updated_at

            // echo "Word : $word , Source : $source_name , Author : $author.<br>";
            $trengkasBaru = new Trengkas(['word' => $word, 'source' => $source_name . '.png', 'created_at' => $date, 'updated_at' => $date]);
            $trengkasBaru->save();
        }
    }
    public function semak(Request $request)
    {
        $pre_ayat = '';
        $input_data = $request->get('input_data') . ' END_OF_DATA';
        $this->list_data = explode(' ', $input_data);
        // return json_encode($this->list_data);
        return $this->searchData($this->list_data, '');
    }

    public $try_count = 0;
    function searchData($list_data, $sambunganAyat)
    {
        if (count($list_data) > 0) {
            // echo 'sambungan ayat : '.$sambunganAyat.' counter : '.$this->try_count.'<br>';
            $ayat_carian = $sambunganAyat . $list_data[0];
            // echo "Ayat Carian : $ayat_carian.<br>";
            $isDataExist = Trengkas::where('word', 'like', trim($ayat_carian) . '%')->count() > 0 ? true : false;
            // return Trengkas::where('word','like',$ayat_carian.'%')->get();
            if ($list_data <= 1) {
                $ayat_baru = $ayat_carian;
            } else {
                $ayat_baru = $ayat_carian . ' ';
            }
            $data_semakan_terakhir = $list_data[0];
            unset($list_data[0]);
            // echo 'mula dengan  : ' . $ayat_carian . '<br>';
            // echo json_encode($list_data);
            if (count($list_data) == 0) {
                $data_carian_sebenar = Trengkas::where('word', 'like', trim($ayat_carian) . '%')->first();
                // echo 'data tamat untuk ' . $ayat_carian . ' -- endlen<br>';
                if ($isDataExist) {
                    array_push($this->senarai_sumber, ['word' => $data_carian_sebenar->word, 'source' => $data_carian_sebenar->source, 'found' => true]);
                    echo json_encode($this->senarai_sumber);
                } else {
                    $data_carian_sebenar = Trengkas::where('word', 'like', trim($sambunganAyat) . '%')->first();
                    array_push($this->senarai_sumber, ['word' => trim($sambunganAyat), 'source' => $data_carian_sebenar->source, 'found' => true]);
                    $list_data = array_merge([$data_semakan_terakhir], $list_data);
                    $this->try_count = 0;
                    $this->searchData($list_data, '');
                }
            } else {

                $list_data = $this->resetArray($list_data);
                // return $list_data;
                if ($isDataExist) {

                    // echo 'data wujud';
                    $this->try_count += 1;
                    $this->searchData($list_data, $ayat_baru);
                } else {
                    if ($this->try_count == 0) {
                        // echo 'data tamat untuk ' . $ayat_carian . ' - tidak jumpa counter = '.$this->try_count.'<br>';

                        array_push($this->senarai_sumber, ['word' => $ayat_carian, 'source' => 'empty', 'found' => false]);
                        $this->searchData($list_data, '');
                        
                    } else {
                      
                        $isDataExist_ = Trengkas::where('word', 'like', trim($sambunganAyat) . '%')->count() > 0 ? true : false;
                        
                        // echo 'sambungan ayat : '.$sambunganAyat.' counter : '.$this->try_count.'<br>';
                        // echo 'data tamat untuk ' . $ayat_carian . ' - habis carian counter : '.$this->try_count.'<br>';
                        if($isDataExist_){
                            $data_carian_sebenar = Trengkas::where('word', 'like', '%'.trim($sambunganAyat).'%')->first();
                            array_push($this->senarai_sumber, ['word' => $data_carian_sebenar->word, 'source' => $data_carian_sebenar->source , 'found' => true]);
                            $list_data = array_merge([$data_semakan_terakhir], $list_data);
                            $this->try_count = 0;
                            $this->searchData($list_data, '');

                        }   else{
                            array_push($this->senarai_sumber, ['word' => $sambunganAyat, 'source' => 'empty', 'found' => false]);
                            $list_data = array_merge([$data_semakan_terakhir], $list_data);
                            $this->try_count = 0;
                            $this->searchData($list_data, '');
                        }                     
                    }
                }
            }
        } else {
            return json_encode($this->senarai_sumber);
        }
    }
    function resetArray($array)
    {
        $newArray = [];
        foreach ($array as $arr) {
            array_push($newArray, $arr);
        }
        return $newArray;
    }
}

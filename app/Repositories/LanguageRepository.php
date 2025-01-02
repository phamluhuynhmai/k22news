<?php

namespace App\Repositories;

use App\Models\Language;
use File;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class LanguageRepository
 * Repository xử lý các thao tác liên quan đến ngôn ngữ trong hệ thống
 */
class LanguageRepository extends BaseRepository
{
    /**
     * Các trường có thể tìm kiếm
     * @var array
     */
    protected $fieldSearchable = [
        'language',  // Tên ngôn ngữ
        'iso_code',  // Mã ISO của ngôn ngữ
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Language::class;
    }

    /**
     * Tạo file dịch cho ngôn ngữ mới
     * @param array $input Dữ liệu đầu vào chứa iso_code của ngôn ngữ
     * @return bool
     */
    public function translationFileCreate($input): bool
    {
        $allLanguagesArr = [];
        // Lấy danh sách thư mục ngôn ngữ hiện có
        $languages = File::directories(base_path('lang'));
       
        // Lấy mã ISO từ tên thư mục
        foreach ($languages as $language) {
            $allLanguagesArr[] = substr($language, -2);
        }

        // Kiểm tra nếu ngôn ngữ đã tồn tại
        if (in_array($input['iso_code'], $allLanguagesArr)) {
            throw new UnprocessableEntityHttpException($input['iso_code'].' language already exists.');
        }

        try {
            if (! empty($input['iso_code'])) {
                // Tạo thư mục mới cho ngôn ngữ
                File::makeDirectory(lang_path().'/'.$input['iso_code']);

                // Sao chép tất cả file từ thư mục 'en' sang thư mục ngôn ngữ mới
                $filesInFolder = File::files(App::langPath().'/en');
                
                foreach ($filesInFolder as $path) {
                    $file = basename($path);
                    File::copy(App::langPath().'/en/'.$file, App::langPath().'/'.$input['iso_code'].'/'.$file);
                }
            }

            return true;
        } catch (\Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * Kiểm tra sự tồn tại của ngôn ngữ
     * @param string $selectedLang Mã ngôn ngữ cần kiểm tra
     * @return bool True nếu ngôn ngữ tồn tại, False nếu không
     */
    public function checkLanguageExistOrNot($selectedLang)
    {
        $langExists = true;
        $allLanguagesArr = [];
        try {
            $languages = File::directories(base_path('lang'));
            
            foreach ($languages as $language) {
                $allLanguagesArr[] = substr($language, -2);
            }

            if (! in_array($selectedLang, $allLanguagesArr)) {
                $langExists = false;
            }

            return $langExists;
        } catch (\Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * Kiểm tra sự tồn tại của file dịch
     * @param string $selectedLang Ngôn ngữ được chọn
     * @param string $selectedFile Tên file cần kiểm tra
     * @return bool True nếu file tồn tại, False nếu không
     */
    public function checkFileExistOrNot($selectedLang, $selectedFile)
    {
        $fileExists = true;
        $data['allFiles'] = [];
        try {
            $files = File::allFiles(App::langPath().'/'.$selectedLang.'/');
            foreach ($files as $file) {
                $data['allFiles'][] = ucfirst(basename($file));
            }

            if (! in_array(ucfirst($selectedFile), $data['allFiles'])) {
                $fileExists = false;
            }

            return $fileExists;
        } catch (\Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * Lấy danh sách file trong thư mục con
     * @param string $selectedLang Ngôn ngữ được chọn
     * @param string $selectedFile File được chọn
     * @return array Mảng chứa thông tin về files và ngôn ngữ
     */
    public function getSubDirectoryFiles($selectedLang, $selectedFile)
    {
        $data['allFiles'] = [];
        try {
            $files = File::allFiles(App::langPath().'/'.$selectedLang.'/');
            foreach ($files as $file) {
                $data['allFiles'][basename($file)] = ucfirst(basename($file));
            }
    
            $data['languages'] = File::directories(base_path('lang'));
            $data['allLanguagesArr'] = [];
            foreach ($data['languages'] as $language) {
                $lName = substr($language, -2);
                $data['allLanguagesArr'][$lName] = strtoupper(substr($language, -2));
                app()->setLocale(substr($selectedLang, -2));
                $data['languages'] = trans(pathinfo($selectedFile, PATHINFO_FILENAME));
            }

            return $data;
        } catch (\Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }
}

<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Http\Request;
use App\Services\FileUploadService;



class CurrencyService
{

    public function __construct(
        public readonly Request $request
    ) {}


    public function validate(): static
    {
        $this->request->validate([
            'title' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'unique:currencies,code', 'max:10'],
            'symbol' => ['required', 'string', 'max:10'],
            'type' => ['required', 'in:fiat,crypto'],
            'icon' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,svg',
                'max:2048', // максимальный размер 2MB
                // 'dimensions:min_width=100,min_height=100,max_width=1000,max_height=1000'
            ],
        ]);

        return $this;
    }



    public function create(): void
    {
        $fileUpload = $this->getFileUpload();

        Currency::create([
            'title' => $this->request->post('title'),
            'code' => $this->request->post('code'),
            'symbol' => $this->request->post('symbol'),
            'type' => $this->request->post('type'),
            'icon' => $fileUpload->getFileName(),
        ]);
    }

    public function update(int $id): void
    {
        $currency = Currency::find($id);

        $data = [
            'title' => $this->request->post('title'),
            'code' => $this->request->post('code'),
            'symbol' => $this->request->post('symbol'),
            'type' => $this->request->post('type'),
            'icon' => $currency->icon,
        ];

        if ($this->request->hasFile('icon')) {
            $fileUpload = $this->getFileUpload();
            $data['icon'] = $fileUpload->getFileName();
        }

        if ($currency && $this->isDataDifferent($currency, $data)) {
            $currency->update($data);
        }
    }


    private function isDataDifferent($currency, $data): bool
    {
        return ($currency->title != $data['title'] ||
            $currency->code != $data['code'] ||
            $currency->symbol != $data['symbol'] ||
            $currency->type != $data['type'] ||
            ($data['icon'] && $currency->icon != $data['icon']));
    }



    public function getFileUpload(): FileUploadService
    {
        $fileUpload = new FileUploadService($this->request->file('icon'), 'currencies');
        $fileUpload->upload();
        return $fileUpload;
    }
}

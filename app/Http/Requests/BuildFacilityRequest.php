<?php
declare(strict_types=1);
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class BuildFacilityRequest extends FormRequest { public function authorize():bool{return true;} public function rules():array{return ['location_id'=>['required','integer','exists:build_locations,id'],'type'=>['required','in:solar,wind,gas,hydro,battery']];} }

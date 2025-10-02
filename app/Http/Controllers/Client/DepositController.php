<?php

namespace App\Http\Controllers\Client;

use Carbon\Carbon;
use App\Models\Bank;
use App\Models\Config;
use App\Models\Deposit;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class DepositController extends Controller
{

    public $coinBankPercent;
    public $coinPayPalPercent;
    public $coinCardPercent;

    public $coinExchangeRate;
    public $coinPayPalRate;

    public function __construct()
    {
        $this->coinBankPercent = Config::getConfig('coin_bank_percentage', 15);
        $this->coinPayPalPercent = Config::getConfig('coin_paypal_percentage', 0);
        $this->coinCardPercent = Config::getConfig('coin_card_percentage', 30);

        $this->coinExchangeRate = Config::getConfig('coin_exchange_rate', 100);
        $this->coinPayPalRate = Config::getConfig('coin_paypal_rate', 20000);
    }

    public function index()
    {
        $user = Auth::user();
        $banks = Bank::where('status', true)->get();
        $deposits = Deposit::where('user_id', $user->id)->latest()->paginate(10);
        
        $coinExchangeRate = $this->coinExchangeRate;
        $coinBankPercent = $this->coinBankPercent;

        return view('pages.information.deposit.deposit', compact(
            'banks', 
            'deposits',
            'coinExchangeRate',
            'coinBankPercent'
        ));
    }
}

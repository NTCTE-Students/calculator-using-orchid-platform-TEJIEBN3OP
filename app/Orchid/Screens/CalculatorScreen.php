<?php

namespace App\Orchid\Screens\Calculator;

use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\TextArea;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CalculatorScreen extends Screen
{
    /** 
    * Display header name.
    *
    * @var string 
    */
    public $name='Калькулятор';

    /** 
    * Display header description.
    *
    * @var string|null 
    */
    public $description='Многофункциональный калькулятор';

    /** 
    * Query data.
    *
    * @return array 
    */
    public function query(): array
    {
        return [
            'first_number' => Session::get('first_number', 0),
            'second_number' => Session::get('second_number', 0),
            'operation' => Session::get('operation', '+'),
            'result' => Session::get('result', null),
            'history' => Session::get('history', []),
        ];
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [
            Button::make('Рассчитать')
            ->icon('calculator')
            ->method('calculate')
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('first_number')
                ->title('Первое число')
                ->type('number')
                ->value(request()->get('first_number')),
                Input::make('second_number')
                ->title('Второе число')
                ->type('number')
                ->value(request()->get('second_number')),
                Select::make('operation')
                ->title('Операция')
                ->options([
                    '+'=>'Сложение',
                    '-'=>'Вычитание',
                    '*' => 'Умножение',
                    '/'=>'Деление',
                    '%' => 'Остаток от деления',
                    '//' => 'Целочисленное деление',
                    '^' => 'Возведение в степень',
                    'sqrt' => 'Квадратный корень',
                    'log' => 'Логарифм',
                    'sin' => 'Синус',
                    'cos' => 'Косинус',
                    'tan' => 'Тангенс',
                ])
            
            ->value(request()->get('operation')),
            ]),
            Layout::rows([
                TextArea::make('result')
                ->title('Результат')
                ->rows(1)
                ->readonly()
                ->value(request()->get('result')),
            ]),
        ];
    }

    /** 
    * @param \Illuminate\Http\Request $request
    */
    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function calculate(Request $request)
    {
        $firstNumber = $request->input('first_number');
        $secondNumber = $request->input('second_number');
        $operation = $request->input('operation');

        $result = null;

        switch ($operation) {
            case '+':
                $result = $firstNumber + $secondNumber;
                break;
            case '-':
                $result = $firstNumber - $secondNumber;
                break;
                    case '*':
                        $result = $firstNumber * $secondNumber;
                    break;
                    case '/':
                         if($secondNumber!==0){
                            $result=$firstNumber / $secondNumber;
                         } else{
                            $result='';
                         }
                    break;
                    case '%':
                        $result=$firstNumber % $secondNumber; 
                    break;
                    case 'intdiv':
                        $result=intidiv($firstNumber, $secondNumber);
                    break;
                    case 'power':
                        $result=pow($firstNumber, $secondNumber);
                    break;
                    case 'sqrt':
                        $result=sqrt($firstNumber);
                    break;
                    case 'log':
                        $result=log($firstNumber);  
                        break;
                        case 'sine':
                            $result = sin($firstNumber);
                            break;
                        case 'cosine':
                            $result = cos($firstNumber);
                            break;
                        case 'tangent':
                            $result = tan($firstNumber);
                            break;     
        }
        return redirect()->back()->withInput(['result' => $result]);
    }
}

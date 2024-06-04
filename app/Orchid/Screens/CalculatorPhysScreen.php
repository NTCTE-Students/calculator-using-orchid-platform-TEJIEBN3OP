<?php

namespace App\Orchid\Screens\CalculatorPhys;

use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\TextArea;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CalculatorPhysScreen extends Screen
{
    /** 
    *  Display header name.
    * 
    *  @var string
    */
    public $name = 'Калькулятор физических величин';

    /** 
    *  Display header description.
    * 
    *  @var string|null
    */
    public $description = 'Преобразуйте единицы измерения';

    /** 
    *  Query data.
    * 
    *  @return array
    */
    public function query(): array
    {
        return [
            'value' => Session::get('value', 0),
            'fromUnit' => Session::get('fromUnit', 'м'),
            'toUnit' => Session::get('toUnit', 'см'),
            'category' => Session::get('category', 'длина'),
            'result' => Session::get('result', null),
            'history' => Session::get('history', []),
        ];
    }

    /** 
    *  Button commands.
    * 
    *  @return \Orchid\Screen\Action[]
    */
    public function commandBar(): array
    {
        return [
            Button::make('Преобразовать')
                ->icon('calculator')
                ->method('calculate'),
        ];
    }

    /**
    *  Views.
    * 
    *  @return \Orchid\Screen\Layout[]
    */
    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('value')
                    ->title('Значение')
                    ->type('number')
                    ->value(request()->get('value')),
                Select::make('category')
                    ->title('Категория')
                    ->options([
                        'длина' => 'Длина',
                        'масса' => 'Масса',
                    ])
                    ->value(request()->get('category')),
                Select::make('fromUnit')
                    ->title('Исходная единица')
                    ->options($this->getUnits(request()->get('category')))
                    ->value(request()->get('fromUnit')),
                Select::make('toUnit')
                    ->title('Единица преобразования')
                    ->options($this->getUnits(request()->get('category')))
                    ->value(request()->get('toUnit')),
            ]),
            Layout::rows([
                TextArea::make('result')
                    ->title('Результат')
                    ->rows(1)
                    ->readonly()
                    ->value(request()->get('result')),
            ]),
            Layout::rows([
                Layout::legend('История вычислений'),
                TextArea::make('history')
                    ->rows(5)
                    ->readonly()
                    ->value($this->formatHistory(request()->get('history'))),
            ]),
        ];
    }

    /** 
    *  @param \Illuminate\Http\Request $request
    * 
    *  @return \Illuminate\Http\RedirectResponse
    */
    public function calculate(Request $request)
    {
        $value = $request->input('value');
        $fromUnit = $request->input('fromUnit');
        $toUnit = $request->input('toUnit');
        $category = $request->input('category');

        $result = $this->convertUnits($value, $fromUnit, $toUnit, $category);

        $history = Session::get('history', []);
        $history[] = [
            'value' => $value,
            'fromUnit' => $fromUnit,
            'toUnit' => $toUnit,
            'category' => $category,
            'result' => $result,
        ];

        Session::put([
            'value' => $value,
            'fromUnit' => $fromUnit,
            'toUnit' => $toUnit,
            'category' => $category,
            'result' => $result,
            'history' => $history,
        ]);

        return redirect()->route('platform.calculator.index');
}
}
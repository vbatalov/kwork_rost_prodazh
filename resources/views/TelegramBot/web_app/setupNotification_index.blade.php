<x-default title="Получение уведомлений по сделкам с древесиной">

    <div class="w-full">
        <div class="companies-list">
            @if(!(auth()->user()->companies->empty()))

                @foreach(auth()->user()->companies as $company)
                    <div>
                        Company: {{$company->partyName}}
                    </div>
                @endforeach
            @else
                Вы ещё не добавили компании для получения уведомлений.
            @endif
        </div>

        <div class="mt-4">
            <a wire:navigate href="{{route("add-company")}}">
                <x-other.buttonBlock text="Управление моими компаниями"/>
            </a>
            <x-other.buttonBlock text="Интеграция с расширением"/>
        </div>

    </div>


</x-default>

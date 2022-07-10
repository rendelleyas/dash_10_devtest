<x-layout>
    <main>
        <player :data=" {{json_encode($players)}} " :active=" {{ isset($activePlayer)? json_encode($activePlayer): "'empty'"}} "/>
    </main>
</x-layout>

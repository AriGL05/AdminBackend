<!--Alguien haga mono este diseño, esto es puro html pelon help, y q llame a la ruta de put de actors-->
@if(isset($actor))
    <h1>{{ $actor->first_name }}</h1>
    <h1>{{ $actor->last_name }}</h1>
    @else
    <p>Actor not found.</p>
@endif

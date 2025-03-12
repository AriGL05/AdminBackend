<!--Alguien haga mono este diseño, esto es puro html pelon help, y q llame a la ruta de put de films-->
@if(isset($film))
    <h1>{{ $film->title }}</h1>
    <p>Release Year: {{ $film->release_year }}</p>
    <p>Length: {{ $film->length }}</p>
    <p>Description: {{ $film->description }}</p>
    @else
    <p>Film not found.</p>
@endif
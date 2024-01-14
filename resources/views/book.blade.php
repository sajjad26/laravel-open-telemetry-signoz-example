@extends('layouts.master')

@section('content')
    <h1>{{ $book->name }}</h1>
    <p>{{ $book->summary }}</p>
    <div>
        <h3>Authors</h3>
        <ul>
            @foreach ($book->authors as $author)
                <li>{{ $author->name }}</li>
            @endforeach
        </ul>
    </div>
    <div>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequuntur cupiditate asperiores porro excepturi,
            fugiat, veniam facere harum dignissimos qui quod quisquam placeat mollitia inventore, rem saepe repudiandae
            cumque ex similique.</p>
        <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quos, incidunt eum. Sed, doloremque. Ex, deserunt
            distinctio, necessitatibus soluta voluptatem similique facilis natus nihil officiis labore cumque quis ipsam
            molestiae atque?</p>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Vitae reprehenderit error cum optio nesciunt blanditiis
            esse non deserunt, ipsam animi ea, sint quo. Ducimus maiores reprehenderit repellendus, aut tempora
            exercitationem.</p>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequuntur cupiditate asperiores porro excepturi,
            fugiat, veniam facere harum dignissimos qui quod quisquam placeat mollitia inventore, rem saepe repudiandae
            cumque ex similique.</p>
        <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quos, incidunt eum. Sed, doloremque. Ex, deserunt
            distinctio, necessitatibus soluta voluptatem similique facilis natus nihil officiis labore cumque quis ipsam
            molestiae atque?</p>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Vitae reprehenderit error cum optio nesciunt blanditiis
            esse non deserunt, ipsam animi ea, sint quo. Ducimus maiores reprehenderit repellendus, aut tempora
            exercitationem.</p>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequuntur cupiditate asperiores porro excepturi,
            fugiat, veniam facere harum dignissimos qui quod quisquam placeat mollitia inventore, rem saepe repudiandae
            cumque ex similique.</p>
        <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quos, incidunt eum. Sed, doloremque. Ex, deserunt
            distinctio, necessitatibus soluta voluptatem similique facilis natus nihil officiis labore cumque quis ipsam
            molestiae atque?</p>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Vitae reprehenderit error cum optio nesciunt blanditiis
            esse non deserunt, ipsam animi ea, sint quo. Ducimus maiores reprehenderit repellendus, aut tempora
            exercitationem.</p>

        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequuntur cupiditate asperiores porro excepturi,
            fugiat, veniam facere harum dignissimos qui quod quisquam placeat mollitia inventore, rem saepe repudiandae
            cumque ex similique.</p>
        <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quos, incidunt eum. Sed, doloremque. Ex, deserunt
            distinctio, necessitatibus soluta voluptatem similique facilis natus nihil officiis labore cumque quis ipsam
            molestiae atque?</p>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Vitae reprehenderit error cum optio nesciunt blanditiis
            esse non deserunt, ipsam animi ea, sint quo. Ducimus maiores reprehenderit repellendus, aut tempora
            exercitationem.</p>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequuntur cupiditate asperiores porro excepturi,
            fugiat, veniam facere harum dignissimos qui quod quisquam placeat mollitia inventore, rem saepe repudiandae
            cumque ex similique.</p>
        <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quos, incidunt eum. Sed, doloremque. Ex, deserunt
            distinctio, necessitatibus soluta voluptatem similique facilis natus nihil officiis labore cumque quis ipsam
            molestiae atque?</p>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Vitae reprehenderit error cum optio nesciunt blanditiis
            esse non deserunt, ipsam animi ea, sint quo. Ducimus maiores reprehenderit repellendus, aut tempora
            exercitationem.</p>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequuntur cupiditate asperiores porro excepturi,
            fugiat, veniam facere harum dignissimos qui quod quisquam placeat mollitia inventore, rem saepe repudiandae
            cumque ex similique.</p>
        <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quos, incidunt eum. Sed, doloremque. Ex, deserunt
            distinctio, necessitatibus soluta voluptatem similique facilis natus nihil officiis labore cumque quis ipsam
            molestiae atque?</p>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Vitae reprehenderit error cum optio nesciunt blanditiis
            esse non deserunt, ipsam animi ea, sint quo. Ducimus maiores reprehenderit repellendus, aut tempora
            exercitationem.</p>
    </div>
@endsection

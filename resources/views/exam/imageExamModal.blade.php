<div class="col-12">
<div class="card card-primary">
    <div class="card-body">
        <div class="row">
        @foreach($data as $imgQuestion)
        <div class="col-sm-2">
            <a href="/images/{{ $imgQuestion->image }}" data-toggle="lightbox" data-gallery="gallery">
            <img src="/images/{{ $imgQuestion->image }}" class="img-fluid mb-2" alt="white sample"/>
            </a>
        </div>
        @endforeach
        </div>
    </div>
    </div>
</div>
</div>
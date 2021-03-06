
<section class="pricing-plan bg-gredient3">
    <div class="container our-system">
                        <h3>Nos Tarifs</h3>
        <div class="row">
            @foreach ($plans as $key => $plan)
                <div class="col-lg-3 col-md-4 col-sm-6 py-2">
                    <div class="plan-2">
                        <h6 class="text-center">{{ $plan->name }}</h6>
                        <p class="price">
                            <sup>{{(env('CURRENCY_SYMBOL') ? env('CURRENCY_SYMBOL') : 'DH')}}</sup>
                            {{ number_format($plan->price) }}
                            <sub>/ {{$plan->duration}}</sub>
                        </p>
                        <ul class="plan-detail">
                            <li>{{($plan->max_users==-1)?__('Illimité'):$plan->max_users}} {{__('Utilisateurs')}}</li>
                            <li>{{($plan->max_customers==-1)?__('Illimité'):$plan->max_customers}} {{__('Clients')}}</li>
                            <li>{{($plan->max_venders==-1)?__('Illimité'):$plan->max_venders}} {{__('Fournisseurs')}}</li>
                        </ul>
                        <a href="{{ route('register') }}" class="button">S'inscrire</a>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="container our-system">
        <h3>Contacter nous pour plus d'infos</h3>
    </div>
    </div>
</section>
<div id="ul-section">
    <ul class="list-group list-group-horizontal tooltip1text" style="z-index:1000;">
        <li class="list-group-item"><button class="btn btn-default" id="delete"><i class="fa fa-trash"></i></button></li>
        <li class="list-group-item"><button class="btn btn-default" onclick="copySection(this)" id="copy_btn"><i class="fa fa-copy"></i></button></li>
        <li class="list-group-item"><a class="btn btn-default handle"><i class="fa fa-arrows"></i></a></li>

    </ul>
</div>



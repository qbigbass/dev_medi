
// Яндекс карта

ymaps.ready(init);
var mediMap;
var mediObjectManager;
function init()
{
    if (window.pos === undefined) {
        window.pos = {
            map_x: '59.112935',
            map_y: '83.361806',
            map_scale: '4',
            min_scale: '4'
        };
    }
    mediMap = new ymaps.Map("map-stores", {
        center: [window.pos.map_x, window.pos.map_y],
        zoom: window.pos.map_scale,
        controls: ['geolocationControl', 'fullscreenControl', 'zoomControl', 'routeButtonControl', 'searchControl']
    });

    if (window.pos.min_scale === undefined) {
        window.pos.min_scale = '8';
    }
    mediMap.options.set( {
        minZoom: window.pos.min_scale,
        suppressMapOpenBlock: true,
    });

    mediObjectManager = new ymaps.ObjectManager({
        clusterize: false,
        geoObjectIconLayout: 'default#image',
        geoObjectIconImageHref: '/upload/images/placemarker.png',
        geoObjectIconImageSize: [28, 37],
        geoObjectIconImageOffset: [-14, -37]
    });


    mediMap.geoObjects.add(mediObjectManager);

    if (window.pos.features !== undefined) {
        mediObjectManager.add({
            type: 'FeatureCollection',
            features: window.pos.features
        });

                $(".map-balloon-shedule").each(function(){
                    console.log($(this).html());
                });
    }



}

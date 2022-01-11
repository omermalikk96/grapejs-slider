<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <script src="https://code.jquery.com/jquery-1.11.0.min.js" type="text/javascript"></script>
    <script src="https://code.jquery.com/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>

    <link rel="stylesheet" href="https://unpkg.com/grapesjs/dist/css/grapes.min.css">
    <script src="//unpkg.com/grapesjs"></script>
    <script type="text/javascript" src="{{ asset('/js/slick.min.js') }}"></script>
</head>

<body>
    <style>
        #gjs {
            border: 3px solid #444;
            max-height: 100%;
        }

        /* Reset some default styling */
        /* .gjs-cv-canvas {
        top: 0;
        width: 100%;
        height: 100%;
    }  */
        .gjs-block {
            width: auto;
            height: auto;
            min-height: auto;
        }

    </style>
    <div id="gjs">
        <h1>Hello World Component!</h1>
        {{-- <button>hi there</button> --}}
        {{-- <div id="slickslider" data-gjs-type="slickslider">
            <div class="slide" id="slide1"> <img src="/images/logo.png"> </div>
            <div class="slide" id="slide2"> <img src="/images/home-banner.png"> </div>
        </div> --}}
    </div>
    <div id="blocks"></div>
    <script type="text/javascript">
        const typeInput = editor => {
            editor.DomComponents.addType('typeInput', {
                isComponent: el => el.tagName == 'INPUT',

                model: {
                    defaults: {
                        tagName: 'input',
                        draggable: 'form, form *',
                        droppable: false,
                        highlightable: false,
                        attributes: {
                            type: 'text'
                        },
                        traits: [
                            nameTrait,
                            placeholderTrait,
                            {
                                type: 'select',
                                name: 'type',
                                options: [{
                                        value: 'text'
                                    },
                                    {
                                        value: 'email'
                                    },
                                    {
                                        value: 'password'
                                    },
                                    {
                                        value: 'number'
                                    },
                                ]
                            },
                            requiredTrait
                        ],
                    },
                },

                extendFnView: ['updateAttributes'],
                view: {
                    updateAttributes() {
                        this.el.setAttribute('autocomplete', 'off');
                    },
                }
            });
        };

        const myNewComponentTypes = editor => {
            editor.DomComponents.addType('typeButton', {
                extend: typeInput,
                isComponent: el => el.tagName == 'BUTTON',

                model: {
                    defaults: {
                        tagName: 'button',
                        attributes: {
                            type: 'button'
                        },
                        text: 'Send',
                        traits: [{
                            name: 'text',
                            changeProp: true,
                        }, {
                            type: 'select',
                            name: 'type',
                            options: [{
                                    value: 'button'
                                },
                                {
                                    value: 'submit'
                                },
                                {
                                    value: 'reset'
                                },
                            ]
                        }]
                    },

                    init() {
                        const comps = this.components();
                        const tChild = comps.length === 1 && comps.models[0];
                        // console.log(tChild);
                        const chCnt = (tChild && tChild.is('textnode') && tChild.get('content')) || '';
                        const text = chCnt || this.get('text');
                        console.log(text);

                        this.set({
                            text
                        });
                        this.on('change:text', this.__onTextChange);
                        (text !== chCnt) && this.__onTextChange();
                    },

                    __onTextChange() {
                        this.components(this.get('text'));
                    },
                },

                view: {
                    events: {
                        click: e => e.preventDefault(),
                    },
                },
            });

        };


        const slider = editor => {
            editor.DomComponents.addType('slickslider', {
                // extend: typeInput,
                isComponent: el => el.tagName == 'SLIDER',
                model: {
                    defaults: {
                        allowScripts: 1,
                        classes: ['slickslider'],
                        ccid: '',
                        tagName: 'div',
                        copyable: false,
                        draggable: true,
                        droppable: true,
                        resizable: true,
                        // traits: [{
                        //     // name: 'image',
                        //     type: 'image',
                        //     name: 'image',
                        //     changeProp: true,
                        // }],
                        'speed': 50,
                        'slides-to-scroll': 1,
                        'infinite': false,
                        traits: [{
                                type: 'number',
                                label: 'Slide Speed',
                                name: 'speed',
                                changeProp: 1,
                            },
                            {
                                type: 'checkbox',
                                label: 'Infinite',
                                name: 'infinite',
                                changeProp: 1,
                            },
                            {
                                type: 'number',
                                label: 'Slides to scroll',
                                name: 'slides-to-scroll',
                                changeProp: 1,
                            },
                        ],




                        components: [{
                                type: 'image',
                            },
                            {
                                type: 'image',
                            }

                        ],
                        script: function() {
                            alert('model');
                            var infinite = '{[ infinite ]}';
                            infinite = infinite == 'true' ? 1 : parseInt(infinite, 10);
                            console.log('inside model script')
                            const id = '{[ ccid ]}'
                            try {
                                $('#' + id).slick('unslick');
                            } catch (e) {}
                            $('#' + id).slick({
                                dots: true,
                                infinite: isNaN(infinite) ? false : infinite,
                                speed: parseInt('{[ speed ]}', 10),
                                arrows: true,
                                adaptiveHeight: true,
                                slidesToScroll: parseInt('{[ slides-to-scroll ]}', 10),
                            })
                        }
                    },
                },
                view: {
                    init() {
                        const props = ['speed', 'slides-to-scroll', 'infinite'];
                        const reactTo = props.map(prop => `change:${prop}`).join(' ');
                        this.listenTo(this.model, reactTo, this.render);
                        const comps = this.model.components();

                        alert('in view');
                        const ccid = this.model.ccid
                        this.model.set('ccid', ccid)
                        const viewObj = this
                        const am = editor.AssetManager;
                        const tImageView = am.getType('image').view;
                        am.addType('image', {
                            view: {
                                onClick() {
                                    tImageView.prototype.onClick.apply(this);
                                    // console.log(viewObj);
                                    viewObj.updateScript()
                                    alert('Image Selected Successfully!');

                                    this.em.get('Modal').close();
                                },
                            }
                        })
                    },

                },


            });

        };



        const editor = grapesjs.init({


            // Indicate where to init the editor. You can also pass an HTMLElement
            container: '#gjs',
            allowScripts: 1,
            // Get the content for the canvas directly from the element
            // As an alternative we could use: `components: '<h1>Hello World Component!</h1>'`,
            fromElement: true,
            // Size of the editor
            height: '300px',
            width: 'auto',
            // Disable the storage manager for the moment
            storageManager: false,
            // Avoid any default panel
            // panels: { defaults: [] },
            // scripts: [
            //     'https://code.jquery.com/jquery-1.11.0.min.js',
            //     'https://code.jquery.com/jquery-migrate-1.2.1.min.js'
            // ],
            blockManager: {
                appendTo: '#blocks',
                blocks: [{
                        id: 'section', // id is mandatory
                        label: '<b>Section</b>', // You can use HTML/SVG inside labels
                        attributes: {
                            class: 'gjs-block-section'
                        },
                        content: `<section>
                            <h1>This is a simple title</h1>
                            <div>This is just a Lorem text: Lorem ipsum dolor sit amet</div>
                            </section>`,
                    }, {
                        id: 'text',
                        label: 'Text',
                        content: '<div data-gjs-type="text">Insert your text here</div>',
                    }, {
                        id: 'image',
                        label: 'Image',
                        // Select the component once it's dropped
                        select: true,
                        // You can pass components as a JSON instead of a simple HTML string,
                        // in this case we also use a defined component type `image`
                        content: {
                            type: 'image'
                        },
                        // This triggers `active` event on dropped components and the `image`
                        // reacts by opening the AssetManager
                        activate: true,
                    },
                    {
                        id: 'button',
                        label: 'button',
                        // Select the component once it's dropped
                        select: true,
                        // You can pass components as a JSON instead of a simple HTML string,
                        // in this case we also use a defined component type `image`
                        content: {
                            type: 'typeButton'
                        },
                        // This triggers `active` event on dropped components and the `image`
                        // reacts by opening the AssetManager
                        activate: true,
                    },
                    {
                        id: 'href',
                        label: 'href',
                        // Select the component once it's dropped
                        select: true,
                        // You can pass components as a JSON instead of a simple HTML string,
                        // in this case we also use a defined component type `image`
                        content: {
                            type: 'link'
                        },
                        // This triggers `active` event on dropped components and the `image`
                        // reacts by opening the AssetManager
                        activate: true,
                    },
                ]
            },
            plugins: [myNewComponentTypes, slider],
            // avoidInlineStyle: false
        });

        editor.on(`component:remove`, model => ('Global hook: component:remove', model.parent().view.render()));
        // editor.on(`component:remove`, model => console.log('Global hook: component:remove', model.initMySLider()));
        editor.BlockManager.add('slickslider', {
            label: 'Slick Slider',
            category: 'Media',
            allowScripts: 1,
            attributes: {
                icon: 'fa fa-video'
            },
            content: {

                type: 'slickslider',
                activeOnRender: 1,
                style: {
                    'background-color': 'rgba(0, 0, 0, 0.1)',
                },
                script: function() {
                    //   console.log('here scr');
                    var infinite = '{[ infinite ]}';
                    infinite = infinite == 'true' ? 1 : parseInt(infinite, 10);
                    var initMySLider = function() {
                        alert('coming from delete');
                        console.log('block manager script');
                        const id = '{[ ccid ]}'
                        try {
                            $('#' + id).slick('unslick');
                        } catch (e) {}
                        $('#' + id).slick({
                            dots: true,
                            infinite: isNaN(infinite) ? false : infinite,
                            speed: parseInt('{[ speed ]}', 10),
                            arrows: true,
                            adaptiveHeight: true,
                            slidesToScroll: parseInt('{[ slides-to-scroll ]}', 10),
                        })
                    }
                    var script = document.createElement('script');
                    script.src = 'https://code.jquery.com/jquery-1.11.0.min.js';
                    document.body.appendChild(script);

                    var script = document.createElement('script');
                    script.src = 'https://code.jquery.com/jquery-migrate-1.2.1.min.js';
                    document.body.appendChild(script);

                    var script = document.createElement('script');
                    script.onload = initMySLider;
                    script.src = '/js/slick.min.js';
                    document.body.appendChild(script);


                    var link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.type = 'text/css';
                    link.href = 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.css';

                    var link2 = document.createElement('link');
                    link2.rel = 'stylesheet';
                    link2.type = 'text/css';
                    link2.href = 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.css';
                    document.body.appendChild(link);
                    document.body.appendChild(link2);

                },
                // content: `<div  id="slickslider" data-gjs-type="slickslider"> <div class="slide" id="slide1"><img src="/images/home-banner.png">  </div><div class="slide" id="slide2"><img src="/images/nd.png">   </div></div>`
            }
        });





        editor.TraitManager.addType('buttonCarousel', {
            type: 'button',

        });
    </script>

</body>

</html>

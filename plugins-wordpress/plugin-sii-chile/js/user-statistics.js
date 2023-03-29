

(function ($) {

  /**
   * Iniciamos el intervalos de observacion.
   */
  setInterval(observers, 1000);


  /**
   * Se busca en el localstorage si existe el unique id
   */
  _unique_id = localStorage.getItem('unique_id');

  // en caso de que se encuentre allí, se lo asigna a la constante __user_unique_id
  if(_unique_id != null){
    const __user_unique_id = localStorage.getItem('unique_id');
    const __new_user = 0; // el usuario ya está registrado, ergo, es recurrente
    localStorage.setItem('is_new_user', __new_user);
  }else{
    /** 
     * Caso contrario, se el asigna al usuario un code id random usando Math.random
     * 
     * Code extraido de https://www.codegrepper.com/code-examples/javascript/create+a+unique+session+id+in+javascript 
     * Math.random debe ser único debido a su algoritmo de siembra.
     * Conviértelo a base 36 (números + letras) y toma los primeros 9 caracteres
     * después del decimal.
     * 
     * */     
    var ID_unique = Math.random().toString(36).substr(2, 9);
    const __new_user = 1;
    localStorage.setItem('unique_id', ID_unique);
    localStorage.setItem('is_new_user', __new_user);
  }

  

  /**
   * Recuperamos las constantes que nos sivern para enviar la informacion al server
   */
  const __token = WP_OPTIONS.token;
  const __URLactual = window.location.href
  const __billconnecto_url = WP_OPTIONS.billconnecto_url
  const __user_logued = WP_OPTIONS.user_logued
  const __user_unique_id = localStorage.getItem('unique_id');
  const __new_user = localStorage.getItem('is_new_user');
  console.log(__new_user);

  _to_BC_windows_observer_images_over = localStorage.getItem('windows_observer_images_over')
  if (_to_BC_windows_observer_images_over != null) {
    create(_to_BC_windows_observer_images_over, 'windows_observer_images_over')
  }


  _to_BC_windows_observer_images = localStorage.getItem('windows_observer_images')
  if (_to_BC_windows_observer_images != null) {
    create(_to_BC_windows_observer_images, 'windows_observer_images')
  }

  _to_BC_images_over_iterations = localStorage.getItem('images_over_iterations')
  if (_to_BC_images_over_iterations != null) {
    create(_to_BC_images_over_iterations, 'images_over_iterations')
  }

  _to_BC_image_click = localStorage.getItem('image_click')
  console.log(__user_unique_id)
  if (_to_BC_image_click != null) {
    create(_to_BC_image_click, 'image_click')
  }


/**
 * 
 * @param {String a encriptar} str 
 * @returns 
 */
  function encode(str) {
    var encoded = "";
    for (i=0; i < str.length;i++) {
      var a = str.charCodeAt(i);
      var b = a ^ 30;    // bitwise XOR with any number, e.g. 123
      encoded = encoded+String.fromCharCode(b);
    }
    return encoded;
  }


  /**
   * Enviamos al server la informacion obtenida
   * @param {Informacion Capturada} __bigData 
   * @param {Item a borrar del local storage} __item 
   */


  function create(__bigData, __item) {

    __data = {  token: __token, 
                visitor_id:__user_logued!=0?__user_logued:null, 
                id_usuario:__user_unique_id,
                web_url: __URLactual, 
                big_data: encode(__bigData)
                
              }

    $.ajax({
      url: WP_OPTIONS.save_analitycs,
      type: "post",
      data: __data,
      success: function (result) {
        /**
         * Vaciamos el LocalStorage
         */
        localStorage.removeItem(__item)       
      }, error: function (d, x, v) {
        console.log(d);
        console.log(x);
        console.log(v);
      }
    });
  }


  var cursorX;
  var cursorY;


  var imagesClick = []
  var images = []
  var imagesOver = []
  var mouseOverImageIterations = []

  /**
   * Observamos la veces que el mouse pasa por encima de una imagen.
   */
  imageMouserOverIterations()
  clickInImage()

  document.onmousemove = function (e) {
    cursorX = e.pageX;
    cursorY = e.pageY;
  }






  function observers() {

    /**
     * Observamos la imagenes que estan bajo el puntero del mous y contamos el tiempo
     */
     imageMouseOverCounter()

    /**
     * Observamos la imagenes Visibles
     */
    imagesVisibles()

  }

  function clickInImage()
  {
    var matches = document.querySelectorAll('img');
    matches.forEach(__image_over => {

      __image_over.addEventListener('click', function () {

        const exist = imagesClick.find(objeto => objeto.img === __image_over.src);
  
        if (exist != null) {
          const i = imagesClick.indexOf(exist)
          imagesClick[i].click += 1
        }
        else {
          imagesClick.push({type: 'image_click', img: __image_over.src, click: 1 })
        }
  
        localStorage.setItem('image_click', JSON.stringify(imagesClick))
      })
  
    });
  }


  function imageMouserOverIterations()
  {
    var matches = document.querySelectorAll('img');
    matches.forEach(__image_over => {

      __image_over.addEventListener('click', function () {
        
      })
  
      __image_over.addEventListener('mouseenter', function () {
  
        const exist = mouseOverImageIterations.find(objeto => objeto.img === __image_over.src);
  
        if (exist != null) {
          const i = mouseOverImageIterations.indexOf(exist)
          mouseOverImageIterations[i].iterations += 1
        }
        else {
          mouseOverImageIterations.push({type: 'images_over_iterations', img: __image_over.src, iterations: 1 })
        }
  
        localStorage.setItem('images_over_iterations', JSON.stringify(mouseOverImageIterations))
  
      });
  
    });
  }

  function imageMouseOverCounter()
  {
    if (cursorX != null && cursorY != null) {

      /**
       * Obtenemos el elemento debajo del puntero del Mouse
       */
      __elemento = document.elementFromPoint(cursorX, cursorY)

      /**
       * Verificamos que ese elemento sea una imagen
       */
      if (__elemento && __elemento.tagName == 'IMG') {
        const exist = imagesOver.find(objeto => objeto.img === __elemento.src);

        if (exist != null) {
          const i = imagesOver.indexOf(exist)
          imagesOver[i].time += 1
        }
        else {
          imagesOver.push({type: 'windows_observer_images_over', img: __elemento.src, time: 1,  })
        }

        /**
         * Guardamos en el local Storage
         */
        localStorage.setItem('windows_observer_images_over', JSON.stringify(imagesOver))
      }


    }
  }

  function imagesVisibles() {
    /**
        * Buscamos en todo el documento todos los elementos que son <img>
        */
    const matchesImages = document.querySelectorAll('img');

    matchesImages.forEach(element => {

      /**
       * Verificamos si estan visibles en este momento en la pantalla
       */
      if (esVisible(element)) {

        //Vemos si ya esta agregado al array
        const exist = images.find(objeto => objeto.img === element.src);

        /**Si Existe en el array le sumamos 1 al tiempo */
        if (exist != null) {          
          const i = images.indexOf(exist)          
            images[i].time += 1          
        }
        else {
          /**De no estar disponible, lo agregamos */
          images.push({type: 'windows_observer_images', img: element.src, time: 1 })
        }
      }
    });
    /**
     * Guardamos en el LocalStorage
     */
    localStorage.setItem('windows_observer_images', JSON.stringify(images))
  }



  /**
   * Aqui podemos saber si el elemento esta en la parte visible de la pantalla
   * @param {Elemento a observar} elem 
   * @returns 
   */
  function esVisible(elem) {
    var posTopView = window.scrollY;
    var posButView = posTopView + window.innerHeight;
    var elemTop = elem.offsetTop;
    var elemBottom = elemTop + elem.offsetHeight;
    return ((elemBottom < posButView && elemBottom > posTopView) || (elemTop > posTopView && elemTop < posButView));
  }

})(jQuery);

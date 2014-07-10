class Gallery extends  mx.core.UIObject  {
	var synchronicLoad:Boolean=false
	
	var slideTime:Number=500; //miliseconds
	
	var MaxMainImgWidth:Number;
	var MaxMainImgHeight:Number;
	var align:String='left';
	var valign:String='top';
	
	var useTransitions:Boolean = true;
	var inTransition = {type:mx.transitions.Zoom, direction:0, duration:1, easing:mx.transitions.easing.Bounce.easeInOut};
	var outTransition = {type:mx.transitions.Zoom, direction:1, duration:1, easing:mx.transitions.easing.Bounce.easeInOut};
	
	
	private var mainViewMC:MovieClip=null;
	private var xml:XML=null;
	private var images:Array = new Array();
	private var currentImg:Number=0;
	private var slideShowStarted = false;
	private var intervalResource;
	private var mcLoader:MovieClipLoader;
	private var preloaderLID:String="";
	private var loadedImgs:Number=0;

	
	function Gallery(){
		xml=new XML();
		mcLoader = new MovieClipLoader();
		mcLoader.addListener(this);
	}
	
	function setMainViewMC(in_mainViewMC){
		this.mainViewMC = in_mainViewMC;
		MaxMainImgWidth = this.mainViewMC._width;
		MaxMainImgHeight = this.mainViewMC._height;		
	}

	function setPreloader(pl){
		preloaderLID = pl;
	}
	
	function getImgsCnt(){
		return images.length;
	}
	
	function startSlideShow(){
		if(this.slideShowStarted) return;
		if(this.images.length < 2) return;
		this.slideShowStarted=true;
		this.intervalResource = setInterval(this, "nextImg", this.slideTime);
	}
	
	function stopSlideShow(){
		if(!this.slideShowStarted) return;
		clearInterval(this.intervalResource);
		this.slideShowStarted=false;
	}
	
	function isInSlideShow(){
		return this.slideShowStarted;
	}
	
	function firstImg(){
		if(this.images.length == 0) return;
		if(this.currentImg<this.images.length && this.currentImg>=0){
			this.images[currentImg]._visible = false;
		}
		currentImg = 0;
		this.images[currentImg]._visible = true;
	}
	
	function nextImg(){
		var old = this.currentImg;
		this.currentImg++;
		if(this.currentImg>=this.images.length) 
			this.currentImg = 0;
		this.swapImages(old, this.currentImg);
	}
	
	function prevImg(){
		var old = this.currentImg;
		this.currentImg--;
		if(this.currentImg<0) 
			this.currentImg = this.images.length-1;
		this.swapImages(old, this.currentImg);
	}
	
	function swapImages(oldId, newId){
		if(this.images.length == 0) return;
		if(this.useTransitions){			
			mx.transitions.TransitionManager.start(this.images[oldId], this.outTransition);
			mx.transitions.TransitionManager.start(this.images[newId], this.inTransition);
		} else {
			this.images[oldId]._visible = false;
			this.images[newId]._visible = true;
		}
	}
	
	function fitInBox(width, height, mcObj:MovieClip){
		var xScale = 100;
		var yScale = 100;
		if(mcObj._width>width){
			xScale = width/mcObj._width*100;
		}
		if(mcObj._height>height){
			yScale = height/mcObj._height*100;
		}
		xScale = Math.min(xScale, yScale);
		mcObj._xscale = mcObj._yscale = xScale;
	}
	
	function alignInBox(width, height, mcObj:MovieClip, align, valign){
		switch (align){
			case 'left': mcObj._x=0; break;
			case 'center': mcObj._x= width/2 - mcObj._width/2; break;
			case 'right': mcObj._x= width - mcObj._width; break;
		}
		switch (valign){
			case 'top': mcObj._y=0; break;
			case 'middle': mcObj._y= height/2 - mcObj._height/2; break;
			case 'bottom': mcObj._y= height - mcObj._height; break;
		}
	}
	
	function LoadGallery(file){
		var tmp = new XML();
		var tmp2 = this;

		tmp.onLoad = function(s){
			if(s){
				tmp2.xml = tmp;
				tmp2.preloadImages();
			}
		}

		tmp.load(file);
	}
	
	function clearGallery(){
		stopSlideShow();
		for(var i=0; i<this.images.length; i++){
			this.images[i].removeMovieClip();
		}
		this.images.length = 0;
		this.currentImg = 0;
		loadedImgs = 0;
	}
	
	function preloadImages(){
		var i=0;

		this.clearGallery();
		


		var q:XMLNode;
		var r:XMLNode;
		r = this.xml.firstChild.firstChild;
		var tmp:MovieClip;
		
		for (q = r; q != null; q = q.nextSibling) {

			if(q.nodeType!=1 || q.nodeName!="img") continue;
			tmp = this.createEmptyMovieClip("imgC"+i,this.getNextHighestDepth());
			tmp.imgId = i;
			tmp.imgAttr = q.attributes;
			tmp.imgLoaded = false;
			//if(i>0) 
			tmp._visible = false;
			var img = tmp.createEmptyMovieClip("img",0);

			if(preloaderLID<>"") {
				var pl = tmp.attachMovie(preloaderLID,"pl",1);
				this.fitInBox(MaxMainImgWidth, MaxMainImgHeight, pl);
				this.alignInBox(this.mainViewMC._width, this.mainViewMC._height, pl, this.align, this.valign);
			}
			
			this.images.push(tmp);
			
			if(synchronicLoad){
				if(i==0){
					loadImageById(i);
				}
			} else {
					loadImageById(i);
			}

			
			i++;
		}
		if(this.images.length==0){
			this.onGalleryLoaded();
		}

	}
	
	function loadImageById(id:Number){
		var img = this.images[id].img;
		var src = this.images[id].imgAttr.src;		
		this.mcLoader.loadClip(src, img);		
	}
	
	function onGalleryLoaded(){}
	function onImageLoaded(imgContainer:MovieClip){}
	
	
	function onLoadComplete(tmp:MovieClip) {
		
		if(preloaderLID<>"") {
			tmp._parent.pl.removeMovieClip();
		}

		tmp._parent.imgLoaded = true;
		this.onImageLoaded(tmp._parent);
		loadedImgs++;

		if(loadedImgs==this.images.length){
			this.onGalleryLoaded();
		} else if(synchronicLoad){
			loadImageById(loadedImgs);
		}
	}
	
	function onLoadInit(tmp:MovieClip) {
		this.fitInBox(MaxMainImgWidth, MaxMainImgHeight, tmp);
		this.alignInBox(this.mainViewMC._width, this.mainViewMC._height, tmp, this.align, this.valign);		
//		trace(tmp._name + " w:"+tmp._width+" h:"+tmp._height+" xs:"+tmp._xscale + " x:"+tmp._x+" y:"+tmp._y);	
	}
	
	function onLoadProgress(tmp:MovieClip, bytesLoaded:Number, bytesTotal:Number){
		if(preloaderLID<>"") {
			tmp._parent.pl.setProgress(bytesLoaded, bytesTotal);
		}		
	}
}

define(function(){
    /*
     return the list of points ([ ['c', [x1, y1], [x2, y2] ], ...]) composing the path
     */
    function analyse_path(path){

        var returnValue = [];
        for(var i = 0; i < path.length; i++){
            var pat = path[i];
            switch(pat[0]){
                case 'a':
                    {
                        //elliptical arc
                        break;
                    }
                case 't':
                    {
                        //Shorthand/smooth quadratic Bezier curveto
                        break;
                    }
                case 'q':
                    {
                        //Quadratic Bezier curveto
                        break;
                    }
                case 's':
                    {
                        //shorthand/smooth curveto
                        break;
                    }
                case 'c':
                    {
                        //curveto
                        break;
                    }
                case 'v':
                    {
                        //vertical lineto
                        throw 'file path "v" not supported yet.';
                        break;
                    }
                case 'V':
                    {
                        //vertical lineto
                        throw 'file path "V" not supported yet.';
                        break;
                    }
                case 'h':
                    {
                        //horizontal lineto
                        throw 'file path "h" not supported yet.';
                        break;
                    }
                case 'H':
                    {
                        //horizontal lineto
                        throw 'file path "H" not supported yet.';
                        break;
                    }
                case 'l':
                    {
                        //lineto
                        throw 'file path "l" not supported yet.';
                        break;
                    }
                case 'L':
                    {
                        //lineto
                        returnValue.push({'x' : pat[1], 'y' : pat[2]});
                        break;
                    }
                case 'Z':
                case 'z':
                    {
                        //close path
                        //x1 = current point, x2 = first point
                        throw 'file path "Z" not supported yet.';
                        break;
                    }
                case 'm':
                case 'M':
                    {
                        //moveto
                        returnValue.push({'x' : pat[1], 'y' : pat[2]});
                        break;
                    }
            }
        }
        return returnValue;
    }

    function parseVmlPathString(pathStr){
        var matches = pathStr.match(/[A-z]?([^A-z\s])+\s([^A-z\s])+/g);
        delete matches['input'];
        delete matches['index'];
        delete matches['lastIndex'];
        var len = matches.length;
        var path = [];
        for(var i = 0; i < len; i++){
            var pointStr = matches[i];
            var replaced = false;
            pointStr = pointStr.replace(/^[A-z]{1}/ig, function($0){
                replaced = true;
                return $0 + ' ';
            });
            var point = [];
            if(!replaced){
                point.push('L');
            }
            var coords = pointStr.split(' ');
            for(var j = 0; j < coords.length; j++){
                point.push(coords[j]);
            }
            path.push(point);
        }
        return path;
    }

    function visit_vml(element){

        var store = new Array();
        if((typeof(element) === 'object') && (element.length)){
            for(var i = 0; i < element.length; i++){
                store = store.concat(visit_vml(element[i]));
            }
        }else{
            switch(element.type){
                case 'path':
                    {
                        var pathString = element.attrs.path;
                        var pathArray = parseVmlPathString(pathString);
                        store.push(['path_s', analyse_path(pathArray), element]);
                        break;
                    }
                case 'circle':
                    {
                        store.push(['circle', {
                                radius : element.attrs.r,
                                cx : element.attrs.cx,
                                cy : element.attrs.cy
                            }, element]);
                        break;
                    }
                case 'ellipse':
                    {
                        store.push(['ellipse', {
                                rx : element.attrs.rx,
                                ry : element.attrs.ry,
                                cx : element.attrs.cx,
                                cy : element.attrs.cy
                            }, element]);
                        break;
                    }
                case 'rect':
                    {
                        var point_list = [{
                                x : element.attrs.x,
                                y : element.attrs.y
                            }, {
                                x : element.attrs.x + element.attrs.width,
                                y : element.attrs.y
                            },
                            {
                                x : element.attrs.x + element.attrs.width,
                                y : element.attrs.y + element.attrs.height
                            }, {
                                x : element.attrs.x,
                                y : element.attrs.y + element.attrs.height
                            }, ];
                        //store.push(['rect',[s1,s2,s3,s4],point_list,element]);
                        store.push(['rect', point_list, element]);
                        break;
                    }
            }//end switch
        }
        return store;
    }


    function find_curves_in_path(path)
    {
        var found = false;
        var i = 0;
        var l = path.length;
        while((!found) && (i < l)){
            if(path[i][0] in ['a', 'A', 'c', 'C', 's', 'S', 'q', 'Q', 't', 'T']){
            }
            i++;
        }
        return found;
    }


    function visit_svg(element){

        var store = [];
        if((typeof(element) == 'object') && (element.length)){
            for(var i = 0; i < element.length; i++){
                store = store.concat(visit_svg(element[i]));
            }
        }else{
            switch(element.type){
                case 'path':
                    {
                        var path = analyse_path(element.attrs.path);
                        if(find_curves_in_path(element.attrs.path)){//path with curves
                            store.push(['path_c', path, element]);
                        }else{//path only made of segments (polygon)
                            store.push(['path_s', path, element]);
                        }
                        break;
                    }
                case 'circle':
                    {
                        store.push(['circle', {
                                radius : element.attrs.r,
                                cx : element.attrs.cx,
                                cy : element.attrs.cy
                            }, element]);
                        break;
                    }
                case 'ellipse':
                    {
                        store.push(['ellipse', {
                                rx : element.attrs.rx,
                                ry : element.attrs.ry,
                                cx : element.attrs.cx,
                                cy : element.attrs.cy
                            }, element]);
                        break;
                    }
                case 'image':
                case 'rect':
                    {
                        var point_list = [
                            {x : element.attrs.x, y : element.attrs.y},
                            {x : element.attrs.x + element.attrs.width, y : element.attrs.y},
                            {x : element.attrs.x + element.attrs.width, y : element.attrs.y + element.attrs.height},
                            {x : element.attrs.x, y : element.attrs.y + element.attrs.height}
                        ];
                        store.push(['rect', point_list, element]);
                        break;
                    }
            }

        }
        return store;
    }

    function populate_polygons(raph, element_list)
    {
        var polygons = new Array();
        if(raph.raphael.svg){//svg
            polygons = visit_svg(element_list);
        }else{//vml
            polygons = visit_vml(element_list);
        }
        return polygons;
    }


    var raphaelcollision = function raphaelcollision(raph, element_list, x, y)
    {
        var ret = [];

        var polygons = populate_polygons(raph, element_list);

        var l = polygons.length;
        for(var f = 0; f < l; f++){

            var poly = polygons[f];
            switch(poly[0]){//type
                case 'circle':
                    {
                        if(collide_circle(poly[1], x, y)){//shapeData
                            ret.push(poly);
                        }
                        break;
                    }
                case 'ellipse':
                    {
                        if(collide_ellipse(poly[1], x, y)){
                            ret.push(poly);
                        }
                        break;
                    }
                case 'path_s':
                case 'rect':
                    {
                        if(collide_polygon(poly[1], x, y)){//poly[1] : point list
                            ret.push(poly);
                        }
                        break;
                    }
                default:
                    {
                        throw 'unknown type : ' + poly[0];
                    }
            }
        }
        return ret;
    }

    /*
     poly = list of points : [{x:2,y:4}, {x:5,y:1}, ...]
     poly.length = number of segments
     */
    function collide_polygon(poly, x, y){

        var c = false;
        var points = [];
        var l = poly.length;
        for(var i = 0; i < l; i++){
            if(poly[i] != null){
                points.push(poly[i]);
            }
        }

        l = points.length;
        var j = l - 1;
        for(var i = -1; ++i < l; j = i){
            var cond1 = (points[i].y <= y && y < points[j].y);
            var cond2 = (points[j].y <= y && y < points[i].y);
            var cond3right = parseInt((points[j].x - points[i].x) * (y - points[i].y) / (points[j].y - points[i].y)) + parseInt(points[i].x);
            var cond3 = (x < cond3right);
            if((cond1 || cond2) && cond3){
                c = !c;
            }
        }

        return c;
    }

    function collide_circle(circle, x, y){
        return (circle.radius > Math.sqrt(Math.pow((circle.cx - x), 2) + Math.pow((circle.cy - y), 2)));
    }

    function collide_ellipse(ellipse, x, y){
        return (Math.pow((x - ellipse.cx), 2) / Math.pow(ellipse.rx, 2) + Math.pow((y - ellipse.cy), 2) / Math.pow(ellipse.ry, 2) <= 1);
    }

    return raphaelcollision;
});
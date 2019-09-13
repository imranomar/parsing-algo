<?php


namespace App;
use DB;

class Shortest
{
    public $list = array(); //nodes to visit in iterations
    public $routes = array(); //nodes pushToListto visit in iterations
    public $visited = array(); //already visited nodes
    public $route_totals = array();
    public $current_list = 0;
    public $target;


    public function traverse($id,$target)
    {
        $this->target = $target;
        $this->push((int)$id,0);
        $this->traverse_recurser();
        $shorted_route_length = INF;
        $shortest_routes = array();
        $found_routes = array();



        foreach($this->routes as $route)
        {
            $len = count($route);
            if($route[count($route)-1]==$this->target)
            {
                $found_routes[] = $route;
                if($len<=$shorted_route_length  )
                {
                    $shorted_route_length =  count($route);
                    $shortest_routes[]=  $route;
                }
            }


        }

        return (array(
            'routes' =>json_encode($found_routes),
            'shortest' =>json_encode($shortest_routes)
        ));
    }

    public function traverse_recurser()
    {
        $this->show();
        if(!empty($this->list))
        {
            $node = array_shift($this->list); //node with id and queue it was picked from
            $neighbours = $this->getNeighbourNodes($node[0]);
            //

            if(!empty($neighbours))
            {
                if(count($neighbours)==1)
                {
                    $this->push($neighbours[0],$node[1]);
                }
                else
                {

                    $tmp_route = $this->routes[$node[1]];

                    //push one in the route the node belongs to
                    $poped_neighbour= array_pop($neighbours);
                    $this->push($poped_neighbour,$node[1]);

                    foreach($neighbours as $neighbour)
                    {
                        $this->routes[] = $tmp_route ;

                        $this->push($neighbour,count($this->routes)-1);
                    }

                }

            }
            $this->traverse_recurser();
        }
        else
        {
            //the list is empty now, all  nodes have been visited
            return;
        }
    }

    //@param  int  $id
    //returns ids of neighbours
    function getNeighbourNodes($id)
    {
        $result = array();

        $edges = DB::select("select * from edges where edge_from = ?",[$id]);

        //put found nodes in an array and return it
        foreach ($edges as $edge)
        {
            $result[] = $edge->edge_to;
        }
        return $result;
    }


    public function push($node_id,$route_id)
    {
        if($this->isInRoute($node_id,$route_id))
        {
            return;
        }

        if($this->isInRoute($this->target,$route_id))
        {
            return;
        }

        $this->list[] = array($node_id,$route_id) ;
        $this->routes[$route_id][] = $node_id;
    }
        //@param  int  $neighbour
    //push neighbour nodes  to current list if not already there


    public function isInRoute($node_id,$route_id)
    {
        $found = false;
        if(array_key_exists($route_id,$this->routes))
        {

            if (in_array($node_id,$this->routes[$route_id]))
            {
//                echo ">>>>".$node_id.">>>>";echo ">>>>".$route_id.">>>>";
//                print_r($this->routes[$route_id]);
                $found = true;
            }
        }

        return $found;

    }


    public function show()
    {
        //print_r($this->routes);
    }
}

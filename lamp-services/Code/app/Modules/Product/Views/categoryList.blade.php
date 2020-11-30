@foreach($cat as  $cat1)
 
  @if($cat1->parent_id==0)
    <option value="{{ $cat1->category_id}}" class="parent_cat" style="font-size:13px; color:#000000" disabled>{{ $cat1->cat_name}}</option>
  @endif
    @foreach($parent_id as $parent_cat)
      @if($cat1->category_id == $parent_cat->parent_id)
          @if($parent_cat->is_product_class==0)
           <span><option  class="sub_cat"  value="{{ $cat1->category_id}}" style="font-size:13px; color:#0B6138;" disabled>{{ $parent_cat->cat_name}}</option> </span>
          @endif
           @foreach($product_class as $product_class_cat)
            @if($product_class_cat->is_product_class==1 && $parent_cat->category_id ==$product_class_cat->parent_id )<option value="{{ $product_class_cat->category_id}}" class="prod_class"  style="font-size:14px; font-weight:bold; color:#0174DF !important;">{{ $product_class_cat->cat_name}}</option> 
             @endif
            @endforeach
        
      @endif
    @endforeach 
  
     
@endforeach
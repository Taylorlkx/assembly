<?php

namespace Assembly;

use App\Models\News;
use Illuminate\Http\Request;


class NewsAssembly
{
    public  function index(Request $request)
    {
        $perPage = $request->get('perPage', 10);
        $nodes = new News();
        if($request->get('keyword'))
        {
            $nodes = $nodes->where('title', 'like', '%'.request()->input('keyword').'%');
        }

        $nodes = $nodes->orderBy('id', 'desc');
        $info = $nodes->paginate($perPage)->toArray();

        return $this->returnJSON([
            'count' => $info['total'],
            'rows' => $info['data']
        ]);
    }

    public function show($id)
    {
        $node = News::findOrFail($id);
        $data = $node->toArray();

        return $this->returnJSON($data, 0, 'ok');

    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:128',
            'photo' => 'required',
            'content' => 'required'
        ]);

        $data = array_only($request->all(), ['title',  'photo', 'content']);

        $news = new News();
        $news->fill($data);
        $news->save();

        return $this->returnJSON(null, 0, '新增成功');
    }

    public  function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'max:128'
        ]);

        $data = array_only($request->all(), ['title',  'photo', 'content', 'is_display']);

        $news = News::findorfail($id);
        $news->fill($data);
        $news->save();

        return $this->returnJSON(null, 0, '修改成功');
    }

    public function destroy(Request $request, $id){
        $ids = array_map(function($item) {
            return intval($item);
        }, explode(",", $id));

        if (News::whereIn('id', $ids)->delete()) {
            return $this->returnJSON($ids, 0, '删除成功');
        }
        return $this->returnJSON(null, 1, "删除失败");
    }
}
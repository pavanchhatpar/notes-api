<?php
/**
 * Created by PhpStorm.
 * User: pavan
 * Date: 18/12/2016
 * Time: 11:56 AM
 */

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\Note;
use App\Models\Item;

class NoteController extends Controller
{
    use ApiResponse;

    /**
     * Find all notes of a user.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    {
        $uid = $request->user()->toArray()['id'];
        $data = Note::where('uid', $uid)->get()->toArray();
        foreach($data as $i=>$d) {
            $d['uid'] = $request->user()->toArray();
            $d['items'] = Item::where('nid',$d['id'])->get()->toArray();
            $data[$i] = $d;
        }
        return $this->ok($data);
    }

    /**
     * Find particular note of a user.
     *
     * @param  Request  $request
     * @param  string $id
     * @return \Illuminate\Http\Response
     */
    public function find(Request $request, $id) {
        try {
            $uid = $request->user()->id;
            $data = Note::where([['id', $id],['uid', $uid]])->firstOrFail();
            $data->uid = $request->user();
            $data->items = Item::where('nid',$data->id)->get();
            return $this->ok($data);
        } catch( ModelNotFoundException $e) {
            return $this->notFound(['message'=> 'This note does not exist.']);
        }

    }

    /**
     * Store a new note
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function makeNewNote(Request $request) {
        $user = $request->user();
        $this->validate($request, [
            'items'    => 'required'
        ]);
        $items = $request->items;
        foreach($items as $item) {
            if(!array_key_exists('content', $item) || !array_key_exists('_constructedStringLength', $item)
                || !array_key_exists('read', $item)) {
                return $this->badRequest(['message' => 'Check input fields properly.']);
            }
        }
        $note = new Note;

        $note->uid = $user->id;
        if($request->has('list')) {
            $note->list = $request->list;
        }
        if($request->has('reminder')) {
            $note->reminder = $request->reminder;
        }

        if($note->saveOrFail()) {
            $output = $note->toArray();
            foreach ($items as $i) {
                $item = new Item;

                $item->nid = $note->id;
                if(array_key_exists('checked', $i)) {
                    $item->check = $i['checked'];
                }
                $item->content = $i['content'];
                $item->_constructedStringLength = $i['_constructedStringLength'];
                $item->read = $i['read'];

                $item->saveOrFail();
                $output['items'][] = $item->toArray();
            }

            return $this->created($output);
        } else {
            return $this->serverError(['message' => 'Something went wrong :(']);
        }

    }
}
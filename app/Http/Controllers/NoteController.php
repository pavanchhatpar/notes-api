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
     * @param  integer $id
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
                    $item->checked = $i['checked'];
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

    /**
     * Delete a user's particular note
     *
     * @param Request $request
     * @param integer $id
     * @return \Illuminate\Http\Response
     */

    public function deleteThisNote(Request $request, $id) {
        $uid = $request->user()->id;
        $count = Note::where([['id', $id],['uid', $uid]])->count();
        if($count > 1) {
            return $this->badRequest(['message'=> 'SQL injection is not allowed.']);
        } else if ($count === 0) {
            return $this->notFound(['message'=> 'This note does not exist']);
        }
        $itemsDeleted = Item::where('nid',$id)->delete();
        $noteDeleted = Note::where([['id', $id],['uid', $uid]])->delete();
        if($noteDeleted === 1) {
            return $this->ok([
                'note_id'      => $id,
                'deleted'      => true,
                'itemsDeleted' => $itemsDeleted
            ]);
        } else {
            return $this->serverError(['message' => 'Something went wrong :(']);
        }
    }

    /**
     * Update a user's particular note
     *
     * @param Request $request
     * @param integer $id
     * @return \Illuminate\Http\Response
     */
    public function updateThisNote(Request $request, $id) {
        $uid = $request->user()->toArray()['id'];
        $count = Note::where([['id', $id],['uid', $uid]])->count();
        if($count > 1) {
            return $this->badRequest(['message'=> 'SQL injection is not allowed.']);
        } else if ($count === 0) {
            return $this->notFound(['message'=> 'This note does not exist']);
        }
        if ($request->has('items')) {
            $items = $request->items;
            foreach ($items as $item) {
                if(array_key_exists('id', $item)) {
                    //return $this->badRequest(['message'=>'Item identification missing']);
                    $c = Item::where([['id', $item['id']],['nid', $id]])->count();
                    if($c > 1) {
                        return $this->badRequest(['message'=> 'SQL injection is not allowed.']);
                    } else if ($c === 0) {
                        $msg = 'The item'.$item['id'].' does not exist';
                        return $this->notFound(['message'=> $msg]);
                    }
                    if(array_key_exists('content', $item)) {
                        if(!array_key_exists('_constructedStringLength', $item) || !array_key_exists('read', $item)) {
                            return $this->badRequest(['message'=>'Content information incomplete']);
                        }
                    } else if(array_key_exists('_constructedStringLength', $item) || array_key_exists('read', $item)) {
                        return $this->badRequest(['message'=>'Content information incomplete']);
                    } else if(!array_key_exists('checked', $item) && (!array_key_exists('deleted', $item) || !$item['deleted'])){
                        return $this->badRequest(['message'=>'Nothing to update']);
                    }
                } else {
                    if(!array_key_exists('content', $item)
                    || !array_key_exists('_constructedStringLength', $item) || !array_key_exists('read', $item)) {
                        return $this->badRequest(['message' => 'Content missing']);
                    }
                }
            }
        }
        $data = [];
        if($request->has('list')) {
            $data['list'] = $request->list;
        }
        if($request->has('reminder')) {
            $data['reminder'] = $request->reminder;
        }
        $updatedNotes = Note::where([['uid', $uid],['id', $id]])->update($data);
        if($updatedNotes === 1) {
            if ($request->has('items')) {
                $items = $request->items;
                foreach ($items as $i) {
                    if(array_key_exists('id', $i)) {
                        if(array_key_exists('deleted', $i)) {
                            $deletedCount = Item::where([['id',$i['id']],['nid',$id]])->delete();
                            if($deletedCount > 1) {
                                $this->serverError(['message'=>'Something went wrong :( 1']);
                            }
                        } else {
                            $item = [];
                            if (array_key_exists('checked', $i)) {
                                $item['checked'] = $i['checked'];
                            }
                            if (array_key_exists('content', $i)) {
                                $item['content'] = $i['content'];
                                $item['_constructedStringLength'] = $i['_constructedStringLength'];
                                $item['read'] = $i['read'];
                            }
                            $updatedCount = Item::where([['nid', $id], ['id', $i['id']]])->update($item);
                            if ($updatedCount > 1) {
                                return $this->serverError(['message' => 'Something went wrong :( 2']);
                            }
                        }
                    } else {
                        $item = new Item;

                        $item->nid = $id;
                        if(array_key_exists('checked', $i)) {
                            $item->checked = $i['checked'];
                        }
                        $item->content = $i['content'];
                        $item->_constructedStringLength = $i['_constructedStringLength'];
                        $item->read = $i['read'];

                        $item->saveOrFail();

                    }
                }
            }
            return $this->find($request, $id);
        } else {
            return $this->serverError(['message'=>"Something went wrong :("]);
        }
    }
}
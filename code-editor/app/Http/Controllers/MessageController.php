<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use JWTAuth;

class MessageController extends Controller
{
    /**
     * Get all messages sent or received by the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllMessages()
    {
        $user = JWTAuth::parseToken()->authenticate();

        // Fetch messages sent and received by the user
        $sentMessages = $user->sentMessages;
        $receivedMessages = $user->receivedMessages;

        return response()->json([
            'sentMessages' => $sentMessages,
            'receivedMessages' => $receivedMessages
        ], 200);
    }

    /**
     * Get a specific message by its ID.
     *
     * @param int $messageID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMessage($messageID)
    {
        $user = JWTAuth::parseToken()->authenticate();

        // Find the message
        $message = Message::find($messageID);

        if (!$message) {
            return response()->json([
                'message' => 'Message not found'
            ], 404);
        }

        // Check if the message is sent or received by the user
        if ($message->sender_id !== $user->id && $message->receiver_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized access'
            ], 403);
        }

        return response()->json([
            'message' => $message
        ], 200);
    }

    /**
     * Create a new message.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createMessage(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validatedData = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        // Ensure the receiver exists
        $receiver = User::find($validatedData['receiver_id']);

        if (!$receiver) {
            return response()->json([
                'message' => 'Receiver not found'
            ], 404);
        }

        // Create the message
        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $validatedData['receiver_id'],
            'message' => $validatedData['message'],
        ]);

        return response()->json([
            'message' => 'Message created successfully',
            'messageData' => $message
        ], 201);
    }

    /**
     * Update a specific message.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $messageID
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateMessage(Request $request, $messageID)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validatedData = $request->validate([
            'message' => 'required|string',
        ]);

        // Find the message
        $message = Message::find($messageID);

        if (!$message) {
            return response()->json([
                'message' => 'Message not found'
            ], 404);
        }

        // Check if the message is sent by the user
        if ($message->sender_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Update the message
        $message->update($validatedData);

        return response()->json([
            'message' => 'Message updated successfully',
            'messageData' => $message
        ], 200);
    }

    /**
     * Delete a specific message.
     *
     * @param int $messageID
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMessage($messageID)
    {
        $user = JWTAuth::parseToken()->authenticate();

        // Find the message
        $message = Message::find($messageID);

        if (!$message) {
            return response()->json([
                'message' => 'Message not found'
            ], 404);
        }

        // Check if the message is sent by the user
        if ($message->sender_id !== $user->id) {
            return response()->json([
                'message' => 'Unauthorized access'
            ], 403);
        }

        // Delete the message
        $message->delete();

        return response()->json([
            'message' => 'Message deleted successfully'
        ], 200);
    }
}

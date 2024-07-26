<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;

class MessageController extends Controller
{
     /**
     * Get all messages sent or received by a specific user.
     *
     * @param int $userID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllMessages($userID)
    {
        // Find the user to ensure it exists
        $user = User::find($userID);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

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
     * @param int $userID
     * @param int $messageID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMessage($userID, $messageID)
    {
        // Find the user to ensure it exists
        $user = User::find($userID);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // Find the message
        $message = Message::find($messageID);

        if (!$message) {
            return response()->json([
                'message' => 'Message not found'
            ], 404);
        }

        // Check if the message is sent or received by the user
        if ($message->sender_id !== $userID && $message->receiver_id !== $userID) {
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
     * @param int $userID
     * @return \Illuminate\Http\JsonResponse
     */
    public function createMessage(Request $request, $userID)
    {
        $validatedData = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        // Ensure the user exists
        $user = User::find($userID);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // Create the message
        $message = Message::create([
            'sender_id' => $userID,
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
     * @param int $userID
     * @param int $messageID
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateMessage(Request $request, $userID, $messageID)
    {
        $validatedData = $request->validate([
            'message' => 'required|string',
        ]);

        // Find the user to ensure it exists
        $user = User::find($userID);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // Find the message
        $message = Message::find($messageID);

        if (!$message) {
            return response()->json([
                'message' => 'Message not found'
            ], 404);
        }

        // Check if the message is sent by the user
        if ($message->sender_id !== $userID) {
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
     * @param int $userID
     * @param int $messageID
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMessage($userID, $messageID)
    {
        // Find the user to ensure it exists
        $user = User::find($userID);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // Find the message
        $message = Message::find($messageID);

        if (!$message) {
            return response()->json([
                'message' => 'Message not found'
            ], 404);
        }

        // Check if the message is sent by the user
        if ($message->sender_id !== $userID) {
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

"use client"

import { Card, CardContent, CardFooter, CardHeader } from "@/components/ui/card"
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar"
import { Heart, MessageCircle, FileText, ImageIcon, FileIcon } from "lucide-react"
import { formatDistanceToNow } from "date-fns"
import Link from "next/link"

interface PoemCardProps {
  poem: {
    id: string
    title: string
    content: string
    format: string
    file_url: string | null
    author_username: string
    author_display_name: string
    author_avatar_url: string | null
    likes_count: number
    comments_count: number
    created_at: string
    author_id: string
  }
  isLiked: boolean
  currentUserId?: string
}

export function PoemCard({ poem, isLiked, currentUserId }: PoemCardProps) {
  const formatIcon = {
    text: FileText,
    image: ImageIcon,
    document: FileIcon,
  }[poem.format]

  const Icon = formatIcon || FileText

  return (
    <Link href={`/poem/${poem.id}`}>
      <Card className="h-full hover:shadow-lg transition-shadow cursor-pointer">
        <CardHeader className="space-y-3">
          <div className="flex items-center gap-3">
            <Avatar className="h-10 w-10">
              <AvatarImage src={poem.author_avatar_url || undefined} />
              <AvatarFallback>{poem.author_display_name[0]?.toUpperCase()}</AvatarFallback>
            </Avatar>
            <div className="flex-1 min-w-0">
              <p className="font-medium truncate">{poem.author_display_name}</p>
              <p className="text-sm text-muted-foreground truncate">@{poem.author_username}</p>
            </div>
            <Icon className="h-5 w-5 text-muted-foreground flex-shrink-0" />
          </div>
          <h3 className="text-xl font-bold line-clamp-2">{poem.title}</h3>
        </CardHeader>
        <CardContent>
          {poem.format === "text" ? (
            <p className="text-muted-foreground line-clamp-4 whitespace-pre-line font-serif leading-relaxed">
              {poem.content}
            </p>
          ) : poem.format === "image" && poem.file_url ? (
            <div className="aspect-video relative rounded-lg overflow-hidden bg-muted">
              <img src={poem.file_url || "/placeholder.svg"} alt={poem.title} className="object-cover w-full h-full" />
            </div>
          ) : (
            <div className="flex items-center gap-2 text-sm text-muted-foreground">
              <FileIcon className="h-4 w-4" />
              <span className="truncate">{poem.file_name}</span>
            </div>
          )}
        </CardContent>
        <CardFooter className="flex items-center justify-between text-sm text-muted-foreground">
          <div className="flex items-center gap-4">
            <div className="flex items-center gap-1">
              <Heart className={`h-4 w-4 ${isLiked ? "fill-red-500 text-red-500" : ""}`} />
              <span>{poem.likes_count}</span>
            </div>
            <div className="flex items-center gap-1">
              <MessageCircle className="h-4 w-4" />
              <span>{poem.comments_count}</span>
            </div>
          </div>
          <time>{formatDistanceToNow(new Date(poem.created_at), { addSuffix: true })}</time>
        </CardFooter>
      </Card>
    </Link>
  )
}

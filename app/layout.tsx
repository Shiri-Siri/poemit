import type React from "react"
import type { Metadata } from "next"
import { Geist, Geist_Mono, Crimson_Text } from "next/font/google"
import { Analytics } from "@vercel/analytics/next"
import { createClient } from "@/lib/supabase/server"
import { Navbar } from "@/components/navbar"
import "./globals.css"

const _geist = Geist({ subsets: ["latin"] })
const _geistMono = Geist_Mono({ subsets: ["latin"] })
const _crimsonText = Crimson_Text({
  subsets: ["latin"],
  weight: ["400", "600", "700"],
  variable: "--font-serif",
})

export const metadata: Metadata = {
  title: "PoemIT! - Your Stage for Poetry",
  description: "Write, share, and discover poems from passionate poets around the world",
  generator: "v0.app",
  icons: {
    icon: [
      {
        url: "/icon-light-32x32.png",
        media: "(prefers-color-scheme: light)",
      },
      {
        url: "/icon-dark-32x32.png",
        media: "(prefers-color-scheme: dark)",
      },
      {
        url: "/icon.svg",
        type: "image/svg+xml",
      },
    ],
    apple: "/apple-icon.png",
  },
}

export default async function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode
}>) {
  const supabase = await createClient()
  const {
    data: { user },
  } = await supabase.auth.getUser()

  let profile = null
  if (user) {
    const { data } = await supabase
      .from("profiles")
      .select("username, display_name, avatar_url")
      .eq("id", user.id)
      .single()
    profile = data
  }

  return (
    <html lang="en">
      <body className={`font-sans antialiased ${_crimsonText.variable}`}>
        <Navbar user={user} profile={profile} />
        {children}
        <Analytics />
      </body>
    </html>
  )
}

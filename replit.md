# International Journal of Advanced Research (IJAR)

## Overview

This is a hybrid academic journal website with both static HTML pages and a modern React/Node.js application. The project represents the International Journal of Advanced Research (IJAR), a peer-reviewed multidisciplinary academic journal. The site includes both a traditional HTML structure and a modern React frontend with a Node.js backend for dynamic functionality like contact form submissions.

## User Preferences

Preferred communication style: Simple, everyday language.

## System Architecture

### Frontend Architecture
The project uses a dual frontend approach:
- **Static HTML Pages**: Traditional HTML files (index.html, about.html, contact.html, etc.) with CSS and vanilla JavaScript for basic functionality
- **React SPA**: Modern React application built with TypeScript, using Wouter for routing and Tailwind CSS for styling
- **UI Components**: Comprehensive component library using Radix UI primitives with shadcn/ui styling patterns
- **State Management**: React Query for server state management and form handling

### Backend Architecture
- **Express.js Server**: RESTful API with TypeScript
- **In-Memory Storage**: Currently uses a memory-based storage system for contact form submissions
- **Database Ready**: Configured for PostgreSQL with Drizzle ORM but currently using memory storage
- **Session Management**: Set up for PostgreSQL session storage using connect-pg-simple

### Data Storage Solutions
- **Current**: In-memory storage via MemStorage class for development
- **Planned**: PostgreSQL database with Drizzle ORM for production
- **Schema**: Contact form data with fields for name, email, subject, and message

### Authentication and Authorization
- **Current State**: Basic session setup configured but not actively implemented
- **Architecture**: Prepared for cookie-based sessions with PostgreSQL session store

### Build and Development Tools
- **Vite**: Frontend build tool and development server
- **TypeScript**: Full TypeScript support across client and server
- **ESBuild**: Server-side bundling for production builds
- **Tailwind CSS**: Utility-first CSS framework with custom design system
- **PostCSS**: CSS processing for Tailwind

### API Structure
- **Contact Endpoint**: POST /api/contact for form submissions
- **Admin Endpoint**: GET /api/contacts for retrieving submissions
- **Error Handling**: Centralized error handling with Zod validation
- **Request Logging**: Comprehensive request/response logging middleware

## External Dependencies

### Database and ORM
- **@neondatabase/serverless**: Serverless PostgreSQL driver for Neon database
- **drizzle-orm**: Type-safe ORM for database operations
- **drizzle-zod**: Integration between Drizzle and Zod for schema validation
- **connect-pg-simple**: PostgreSQL session store for Express sessions

### UI and Styling
- **@radix-ui/***: Comprehensive set of unstyled, accessible UI primitives
- **tailwindcss**: Utility-first CSS framework
- **class-variance-authority**: Utility for creating variant-based component APIs
- **clsx**: Utility for constructing className strings

### React Ecosystem
- **@tanstack/react-query**: Server state management and data fetching
- **wouter**: Lightweight React router
- **react-hook-form**: Form handling and validation
- **@hookform/resolvers**: Validation resolvers for react-hook-form

### Development Tools
- **vite**: Build tool and development server
- **typescript**: Static type checking
- **eslint**: Code linting (implied by React setup)
- **@replit/vite-plugin-***: Replit-specific development enhancements

### Validation and Utilities
- **zod**: Runtime type validation and schema definition
- **date-fns**: Date manipulation utilities
- **nanoid**: URL-safe unique ID generator

### Third-party Integrations
- **Neon Database**: Serverless PostgreSQL hosting (configured but not active)
- **Unsplash**: Stock photography for editorial board member images
- **Google Fonts**: Web font delivery for typography
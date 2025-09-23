// Generated TypeScript definitions for BaseApi
// Do not edit manually - regenerate with: ./mason types:generate

export type UUID = string;
export type Envelope<T> = { data: T };

export interface ErrorResponse {
  error: string;
  requestId: string;
  errors?: Record<string, string>;
}

export interface User {
  name: string;
  password: string;
  email: string;
  active: boolean;
  id: string;
  created_at?: any | null;
  updated_at?: any | null;
}

export type GetBenchmarkResponse = Envelope<any>;

export interface GetHealthRequestQuery {
  db?: string;
  cache?: string;
}

export type GetHealthResponse = Envelope<any>;

export interface PostHealthRequestBody {
  db?: string;
  cache?: string;
}

export type PostHealthResponse = Envelope<any>;

export interface PostSignupRequestBody {
  name?: string;
  email?: string;
  password?: string;
}

export type PostSignupResponse = Envelope<User>;

export interface PostLoginRequestBody {
  email?: string;
  password?: string;
}

export type PostLoginResponse = Envelope<{ user: any[] }>;

export type PostLogoutResponse = Envelope<{ message: string }>;

export type GetMeResponse = Envelope<{ user: any[] }>;

export type PostFileUploadResponse = Envelope<{ path: string; url: string; size: number; type: string }>;

export type PostFileUploadResponse = Envelope<{ path: string; url: string; size: number; type: string }>;

export type PostFileUploadResponse = Envelope<{ path: string; url: string; size: number; type: string }>;

export type GetFileUploadResponse = Envelope<any>;

export type DeleteFileUploadResponse = Envelope<any>;

export type GetOpenApiResponse = Envelope<any>;

export type GetDebugExampleResponse = Envelope<any>;

export type GetDebugExampleResponse = Envelope<any>;

export type GetDebugExampleResponse = Envelope<any>;

export type GetDebugExampleResponse = Envelope<any>;

export type GetDebugExampleResponse = Envelope<any>;

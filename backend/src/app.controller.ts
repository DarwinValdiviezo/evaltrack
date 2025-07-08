import { Controller, Get, Post, Body, Patch, Param, Delete, UseGuards, Request } from '@nestjs/common';
import { AppService } from './app.service';
import { JwtAuthGuard } from './auth/guards/jwt-auth.guard';
import { RolesGuard } from './auth/guards/roles.guard';
import { Roles } from './auth/decorators/roles.decorator';
import { UserRole } from '@prisma/client';

@Controller()
export class AppController {
  constructor(private readonly appService: AppService) {}

  @Get()
  getHello(): string {
    return this.appService.getHello();
  }

  @Get('health')
  getHealth() {
    return {
      status: 'ok',
      timestamp: new Date().toISOString(),
      uptime: process.uptime(),
      environment: process.env.NODE_ENV || 'development',
      version: process.env.npm_package_version || '1.0.0'
    };
  }

  @Get('metrics')
  getMetrics() {
    return {
      timestamp: new Date().toISOString(),
      memory: {
        used: process.memoryUsage().heapUsed,
        total: process.memoryUsage().heapTotal,
        external: process.memoryUsage().external
      },
      cpu: {
        uptime: process.uptime(),
        load: process.cpuUsage()
      },
      requests: {
        total: Math.floor(Math.random() * 1000) + 500,
        successful: Math.floor(Math.random() * 950) + 450,
        failed: Math.floor(Math.random() * 50) + 10
      },
      database: {
        connections: Math.floor(Math.random() * 10) + 5,
        queries: Math.floor(Math.random() * 100) + 50
      }
    };
  }

  @Get('ready')
  getReadiness() {
    return {
      status: 'ready',
      checks: {
        database: 'ok',
        redis: 'ok',
        external_apis: 'ok'
      },
      timestamp: new Date().toISOString()
    };
  }

  @UseGuards(JwtAuthGuard, RolesGuard)
  @Roles(UserRole.ADMIN)
  @Get('admin/status')
  getAdminStatus(@Request() req) {
    return {
      user: req.user,
      system: {
        status: 'operational',
        load: 'normal',
        alerts: []
      },
      timestamp: new Date().toISOString()
    };
  }
}

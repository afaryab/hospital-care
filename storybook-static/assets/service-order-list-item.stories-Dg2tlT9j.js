import{c as _,a as L,j as d}from"./utils-q4EKThuO.js";import{B as N}from"./badge-DghL8o_I.js";import{n as E,l as b}from"./index-CSrV_1Wb.js";import"./iframe-DOYQQv_f.js";import"./preload-helper-PPVm8Dsz.js";import"./index-BZoqsebz.js";const y=h=>{const e=_.c(23),{serviceOrder:s,className:v,onClick:r,selected:j}=h,x=j&&"bg-accent",O=r&&"cursor-pointer";let t;e[0]!==v||e[1]!==x||e[2]!==O?(t=L("flex items-center justify-between gap-3 px-3 py-2 rounded-md","hover:bg-accent transition-colors",x,O,v),e[0]=v,e[1]=x,e[2]=O,e[3]=t):t=e[3];const f=r?"button":void 0,g=r?0:void 0;let o;e[4]!==r?(o=r?k=>k.key==="Enter"&&r():void 0,e[4]=r,e[5]=o):o=e[5];let c;e[6]!==s.so_number?(c=d.jsx("p",{className:"text-xs font-mono text-muted-foreground",children:s.so_number}),e[6]=s.so_number,e[7]=c):c=e[7];let a;e[8]!==s.type?(a=d.jsx("p",{className:"text-sm font-medium",children:s.type}),e[8]=s.type,e[9]=a):a=e[9];let n;e[10]!==c||e[11]!==a?(n=d.jsxs("div",{className:"min-w-0",children:[c,a]}),e[10]=c,e[11]=a,e[12]=n):n=e[12];const S=s.so_short??s.departmentKey;let i;e[13]!==S?(i=d.jsx(N,{variant:"outline",className:"text-xs shrink-0",children:S}),e[13]=S,e[14]=i):i=e[14];let m;return e[15]!==r||e[16]!==i||e[17]!==t||e[18]!==f||e[19]!==g||e[20]!==o||e[21]!==n?(m=d.jsxs("div",{className:t,onClick:r,role:f,tabIndex:g,onKeyDown:o,children:[n,i]}),e[15]=r,e[16]=i,e[17]=t,e[18]=f,e[19]=g,e[20]=o,e[21]=n,e[22]=m):m=e[22],m};y.__docgenInfo={description:"",methods:[],displayName:"ServiceOrderListItem"};const R={title:"Elements/ServiceOrder/ServiceOrderListItem",component:y,tags:["autodocs"],parameters:{layout:"padded"}},l={args:{serviceOrder:b}},p={args:{serviceOrder:E}},u={args:{serviceOrder:b,selected:!0}};l.parameters={...l.parameters,docs:{...l.parameters?.docs,source:{originalSource:`{
  args: {
    serviceOrder: mockServiceOrder
  }
}`,...l.parameters?.docs?.source}}};p.parameters={...p.parameters,docs:{...p.parameters?.docs,source:{originalSource:`{
  args: {
    serviceOrder: mockServiceOrderLab
  }
}`,...p.parameters?.docs?.source}}};u.parameters={...u.parameters,docs:{...u.parameters?.docs,source:{originalSource:`{
  args: {
    serviceOrder: mockServiceOrder,
    selected: true
  }
}`,...u.parameters?.docs?.source}}};const C=["OPD","Lab","Selected"];export{p as Lab,l as OPD,u as Selected,C as __namedExportsOrder,R as default};

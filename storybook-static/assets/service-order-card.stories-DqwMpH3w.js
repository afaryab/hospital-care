import{c as k,a as h,j as n}from"./utils-q4EKThuO.js";import{B as C}from"./badge-DghL8o_I.js";import{C as b,a as j}from"./card-tJerkyCy.js";import{l as x,n as N}from"./index-CSrV_1Wb.js";import"./iframe-DOYQQv_f.js";import"./preload-helper-PPVm8Dsz.js";import"./index-BZoqsebz.js";const g=S=>{const e=k.c(19),{serviceOrder:r,className:v,onClick:d}=S,f=d&&"cursor-pointer hover:shadow-md transition-shadow";let s;e[0]!==v||e[1]!==f?(s=h("cursor-default",f,v),e[0]=v,e[1]=f,e[2]=s):s=e[2];let t;e[3]!==r.so_number?(t=n.jsx("p",{className:"text-xs font-mono text-muted-foreground",children:r.so_number}),e[3]=r.so_number,e[4]=t):t=e[4];let a;e[5]!==r.type?(a=n.jsx("p",{className:"font-semibold text-sm mt-0.5",children:r.type}),e[5]=r.type,e[6]=a):a=e[6];let o;e[7]!==t||e[8]!==a?(o=n.jsxs("div",{className:"min-w-0",children:[t,a]}),e[7]=t,e[8]=a,e[9]=o):o=e[9];const O=r.so_short??r.departmentKey;let c;e[10]!==O?(c=n.jsx(C,{variant:"outline",className:"text-xs shrink-0",children:O}),e[10]=O,e[11]=c):c=e[11];let i;e[12]!==o||e[13]!==c?(i=n.jsxs(b,{className:"p-4 flex items-center justify-between gap-3",children:[o,c]}),e[12]=o,e[13]=c,e[14]=i):i=e[14];let m;return e[15]!==d||e[16]!==s||e[17]!==i?(m=n.jsx(j,{className:s,onClick:d,children:i}),e[15]=d,e[16]=s,e[17]=i,e[18]=m):m=e[18],m};g.__docgenInfo={description:"",methods:[],displayName:"ServiceOrderCard"};const P={title:"Elements/ServiceOrder/ServiceOrderCard",component:g,tags:["autodocs"],parameters:{layout:"centered"}},l={args:{serviceOrder:x}},p={args:{serviceOrder:N}},u={args:{serviceOrder:x,onClick:()=>alert("Service order clicked")}};l.parameters={...l.parameters,docs:{...l.parameters?.docs,source:{originalSource:`{
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
    onClick: () => alert('Service order clicked')
  }
}`,...u.parameters?.docs?.source}}};const R=["OPD","Lab","Clickable"];export{u as Clickable,p as Lab,l as OPD,R as __namedExportsOrder,P as default};
